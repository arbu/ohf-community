<?php

use Modules\Volunteers\Entities\Volunteer;

use Carbon\Carbon;

use Faker\Generator as Faker;

$factory->define(Volunteer::class, function (Faker $faker) {
    $gender = $faker->randomElement(['male', 'female']);
    $countryCode = $faker->optional(0.9)->countryCode; 
    $nationality = $countryCode != null && !in_array($countryCode, ['HM', 'BV']) ? \Countries::getOne($countryCode) : null;
    $dob = $faker->optional(0.9)->dateTimeBetween('-70 years', 'now');
    $govtRegNr = $faker->optional(0.2)->randomNumber(5);
    $languages = [];
    for ($i = 0; $i < mt_rand(1,3); $i++) {
        $lc = $faker->languageCode;
        $languages[] = collect(\Languages::lookup([$lc]))->first();
    }
    return [
        'first_name' => $faker->firstName($gender),
        'last_name' => $faker->lastName,
        'date_of_birth' => $dob != null ? Carbon::instance($dob) : null,
        'nationality' => $nationality,
        'gender' => $gender == 'female' ? 'f' : 'm',
        'email' => $faker->optional(0.8)->email,
        'phone' => $faker->optional(0.5)->phoneNumber,
        'whatsapp' => $faker->optional(0.5)->phoneNumber,
        'skype' => $faker->optional(0.5)->userName,
        'passport_id_number' => $faker->optional(0.9)->randomNumber(7),
        'govt_reg_number' => $govtRegNr,
        'govt_reg_expiry' => $govtRegNr != null ? $faker->optional(0.2)->dateTimeBetween('now', '+ 3 months') : null,
        'languages' => $languages,
        'criminal_record_received' => $faker->boolean(70),
        'remarks' => $faker->optional(0.1)->text,
    ];
});
