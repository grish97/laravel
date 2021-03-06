<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Category
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Categories query()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Makes
 *
 * @property int $id unique auto-incremented value
 * @property string $name unique name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Make_Models $make_model
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Models[] $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Makes whereUpdatedAt($value)
 */
	class Makes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Make_Models
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $make_id
 * @property int $model_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Makes[] $make
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Models[] $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models whereMakeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Make_Models whereUpdatedAt($value)
 */
	class Make_Models extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Models
 *
 * @property int $id unique auto-incremented value
 * @property string $name unique name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Make_Models $make_model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Models whereUpdatedAt($value)
 */
	class Models extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Part
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parts query()
 */
	class Part extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $user_name
 * @property int $level
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $ip
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUserName($value)
 */
	class User extends \Eloquent {}
}

