<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
            $table->string('relationship', 30)->default('child');
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('code', 64)->nullable()->unique();
            $table->string('national_id', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('backup_phone', 30)->nullable();
            $table->decimal('sponsorship_amount', 12, 2)->nullable();
            $table->string('education_level')->nullable();
            $table->text('case_details')->nullable();
            $table->boolean('is_patient')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['beneficiary_id', 'relationship'], 'family_members_beneficiary_relationship_idx');
        });

        Schema::create('family_member_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_member_id')->constrained('beneficiary_family_members')->cascadeOnDelete();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->decimal('monthly_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['family_member_id', 'donor_id'], 'family_member_sponsor_unique');
        });

        $this->migrateLegacyFamilyData();
    }

    public function down(): void
    {
        Schema::dropIfExists('family_member_sponsors');
        Schema::dropIfExists('beneficiary_family_members');
    }

    private function migrateLegacyFamilyData(): void
    {
        DB::table('beneficiaries')
            ->select([
                'id', 'family_members_data', 'patient_name', 'patient_age', 'patient_code',
                'monthly_sponsorship_amount', 'notes_cases', 'created_at', 'updated_at',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($beneficiaries): void {
                foreach ($beneficiaries as $beneficiary) {
                    $members = json_decode((string) ($beneficiary->family_members_data ?? ''), true);
                    $sortOrder = 1;

                    foreach (is_array($members) ? $members : [] as $member) {
                        $name = trim((string) ($member['name'] ?? ''));
                        if ($name === '') {
                            continue;
                        }

                        [$birthDate, $age] = $this->parseLegacyAgeOrDate($member['age_dob'] ?? null);
                        DB::table('beneficiary_family_members')->insert([
                            'beneficiary_id' => $beneficiary->id,
                            'relationship' => 'child',
                            'full_name' => $name,
                            'birth_date' => $birthDate,
                            'age' => $age,
                            'code' => $this->uniqueLegacyCode($member['code'] ?? null),
                            'sponsorship_amount' => is_numeric($member['amount'] ?? null) ? $member['amount'] : null,
                            'education_level' => $member['education'] ?? null,
                            'is_patient' => false,
                            'active' => true,
                            'sort_order' => $sortOrder++,
                            'created_at' => $beneficiary->created_at ?? now(),
                            'updated_at' => $beneficiary->updated_at ?? now(),
                        ]);
                    }

                    if (trim((string) ($beneficiary->patient_name ?? '')) !== '') {
                        DB::table('beneficiary_family_members')->insert([
                            'beneficiary_id' => $beneficiary->id,
                            'relationship' => 'patient',
                            'full_name' => trim((string) $beneficiary->patient_name),
                            'age' => is_numeric($beneficiary->patient_age) ? (int) $beneficiary->patient_age : null,
                            'code' => $this->uniqueLegacyCode($beneficiary->patient_code),
                            'sponsorship_amount' => $beneficiary->monthly_sponsorship_amount,
                            'case_details' => $beneficiary->notes_cases,
                            'is_patient' => true,
                            'active' => true,
                            'sort_order' => 20,
                            'created_at' => $beneficiary->created_at ?? now(),
                            'updated_at' => $beneficiary->updated_at ?? now(),
                        ]);
                    }
                }
            });
    }

    private function parseLegacyAgeOrDate(mixed $value): array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return [null, null];
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return [$value, null];
        }

        return [null, ctype_digit($value) ? min(120, (int) $value) : null];
    }

    private function uniqueLegacyCode(mixed $code): string
    {
        $base = strtoupper(trim((string) $code));
        $base = $base !== '' ? $base : 'FM-' . strtoupper(Str::random(8));
        $candidate = $base;
        $suffix = 2;

        while (DB::table('beneficiary_family_members')->where('code', $candidate)->exists()) {
            $candidate = $base . '-' . $suffix++;
        }

        return $candidate;
    }
};
