<?php
namespace App\Services;

use App\Models\Donation;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;

class DonationService
{
    public static function postCreate(Donation $donation): void
    {
        if ($donation->type === 'cash') {
            self::createCashJournal($donation);
        } else {
            // For in-kind, journal entry could reflect inventory asset increase
            self::createInKindJournal($donation);
        }
    }

    protected static function getOrCreateAccount(string $code, string $name, string $type): Account
    {
        $acc = Account::where('code', $code)->first();
        if (!$acc) { $acc = Account::create(['code' => $code, 'name' => $name, 'type' => $type]); }
        return $acc;
    }

    protected static function createCashJournal(Donation $donation): void
    {
        // 1. Determine Asset Account based on cash_channel
        // cash => donation_cash (102)
        // vodafone_cash => donation_Vcash (needs code, e.g., 10202)
        // instapay => donation_instapay (needs code, e.g., 10203)
        // delegate => Logistics_Delivery cash (10201)
        
        $assetAccount = null;
        switch ($donation->cash_channel) {
            case 'vodafone_cash':
                $assetAccount = self::getOrCreateAccount('10202', 'donation_Vcash', 'asset');
                break;
            case 'instapay':
                $assetAccount = self::getOrCreateAccount('10203', 'donation_instapay', 'asset');
                break;
            case 'delegate':
                $assetAccount = self::getOrCreateAccount('10201', 'Logistics_Delivery cash', 'asset');
                break;
            case 'cash':
            default:
                $assetAccount = self::getOrCreateAccount('102', 'donation_cash', 'asset');
                break;
        }

        $donationsRevenue = self::getOrCreateAccount('401', 'Donations Revenue', 'revenue');
        
        $entry = JournalEntry::create([
            'date' => $donation->received_at ? $donation->received_at->toDateString() : now()->toDateString(),
            'entry_type' => $assetAccount->name,
            'gate' => 'donation',
            'locked' => false,
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $assetAccount->id,
            'debit' => $donation->amount ?? 0,
            'credit' => 0,
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $donationsRevenue->id,
            'debit' => 0,
            'credit' => $donation->amount ?? 0,
        ]);
    }

    protected static function createInKindJournal(Donation $donation): void
    {
        $inventory = self::getOrCreateAccount('120', 'Inventory - In Kind', 'asset');
        $donationsRevenue = self::getOrCreateAccount('401', 'Donations Revenue', 'revenue');
        $entry = JournalEntry::create([
            'date' => $donation->received_at ? $donation->received_at->toDateString() : now()->toDateString(),
            'entry_type' => $inventory->name,
            'gate' => 'donation',
            'locked' => false,
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $inventory->id,
            'debit' => $donation->estimated_value ?? 0,
            'credit' => 0,
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $donationsRevenue->id,
            'debit' => 0,
            'credit' => $donation->estimated_value ?? 0,
        ]);
    }
}
