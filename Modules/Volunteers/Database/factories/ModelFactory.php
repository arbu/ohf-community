<?php

use Modules\Volunteers\Entities\Volunteer;
use Modules\Volunteers\Entities\Stay;

use Carbon\Carbon;

use Faker\Generator as Faker;

$factory->define(Volunteer::class, function (Faker $faker) {
    $gender = $faker->randomElement(['male', 'female']);
    $countryCode = $faker->optional(0.9)->countryCode; 
    $nationality = $countryCode != null && !in_array($countryCode, ['HM', 'BV']) ? \Countries::getOne($countryCode) : null;
    $dob = $faker->optional(0.9)->dateTimeBetween('-70 years', '-19 years');
    $govtRegNr = $faker->optional(0.2)->randomNumber(5);
    $languages = [];
    for ($i = 0; $i < mt_rand(1,3); $i++) {
        $lc = $faker->languageCode;
        $languages[] = collect(\Languages::lookup([$lc]))->first();
    }
    return [
        'first_name' => $faker->firstName($gender),
        'last_name' => $faker->lastName,
        'street' => $faker->streetAddress,
        'postcode' => $faker->postcode,
        'city' => $faker->city,
        'country' => $faker->country,
        'emergency_contact' => $faker->optional(0.3)->address,
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
        'has_driving_license' => $faker->optional(0.7)->boolean(70),
        'qualifications' => $faker->optional(0.5)->text,
        'previous_experience' => $faker->optional(0.5)->text,
        'remarks' => $faker->optional(0.1)->text,
    ];
});

$factory->define(Stay::class, function (Faker $faker) {
    $status = $faker->randomElement(['applied', 'confirmed', 'rejected']);
    $arrival = $faker->dateTimeBetween('-6 months', '+3 months');
    $departure = $faker->optional(0.9)->dateTimeBetween($arrival, strtotime('+3 months'));
    $govt_reg_status = $status == 'confirmed' ? $faker->randomElement(['not_yet_applied','applied', 'registered']) : 'not_yet_applied';
    $responsibilities = [];
    for ($i = 0; $i < mt_rand(0, 3); $i++) {
        $responsibilities[] = $faker->jobTitle;
    }    
    return [
        'status' => $status,
        'arrival' => $arrival,
        'departure' => $departure,
        'govt_reg_status' => $govt_reg_status,
        'code_of_conduct_signed' => $status == 'confirmed' && new \DateTime() < $arrival ? $faker->boolean(70) : false,
        'financial_contribution' => $faker->numberBetween(0, 15) * 10,
        'financial_contribution_paid' => $status == 'confirmed' && new \DateTime() < $arrival ? $faker->boolean(70) : false,
        'debriefing_info_received' => $status == 'confirmed' && $departure != null && new \DateTime() < $departure ? $faker->boolean(90) : false,
        'responsibilities' => $responsibilities,
        'remarks' => $faker->optional(0.1)->text,
    ];
});
