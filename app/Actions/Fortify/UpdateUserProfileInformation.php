<?php

namespace App\Actions\Fortify;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  \Illuminate\Foundation\Auth\User $user
     * @param  array<string, mixed> $input
     */
    public function update(\Illuminate\Foundation\Auth\User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'experience' => ['nullable', 'string'], // Ensure experience is validated
            'bio_data' => ['nullable', 'string'], // Ensure bio_data is validated
            'category' => ['nullable', 'string'], // Ensure category is validated
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }

        // Ensure Doctor record is updated
        Doctor::updateOrCreate(
            ['doc_id' => $user->id],
            [
                'experience' => $input['experience'] ?? null,
                'bio_data' => $input['bio_data'] ?? null,
                'category' => $input['category'] ?? null,
            ]
        );
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  \Illuminate\Foundation\Auth\User $user
     * @param  array<string, string> $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
