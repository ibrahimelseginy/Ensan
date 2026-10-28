<?php

use App\Models\GuestHouseMeal;
use App\Models\GuestHouseMealServing;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix past meal servings where patient had no companion but was allocated companion meals
        $servings = GuestHouseMealServing::all();
        $affectedMealIds = [];

        foreach ($servings as $srv) {
            $hasRealCompanion = GuestHouseMealServing::isRealCompanion($srv->companion_name);

            if (!$hasRealCompanion && $srv->companion_meals_count > 0) {
                $pMeals = $srv->patient_meals_count ?: ($srv->received ? 1 : 0);
                $srv->companion_meals_count = 0;
                $srv->companion_received = false;
                $srv->total_meals_count = $pMeals;
                $srv->save();

                $affectedMealIds[$srv->guest_house_meal_id] = true;
            }
        }

        // Recalculate total_meals_served on affected meals
        foreach (array_keys($affectedMealIds) as $mealId) {
            $meal = GuestHouseMeal::find($mealId);
            if ($meal) {
                $newTotal = $meal->servings()
                    ->where('received', true)
                    ->sum(DB::raw('COALESCE(total_meals_count, patient_meals_count + companion_meals_count)'));
                $meal->update(['total_meals_served' => (int)$newTotal]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data fix does not require rollback
    }
};
