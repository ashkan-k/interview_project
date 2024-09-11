<?php

namespace App\Models;

use App\Http\Traits\AuthHelpers;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivationCode extends Model
{
    use HasFactory, AuthHelpers;

    protected $fillable = [
        'code',
        'user_id',
        'is_used'
    ];

    public function generateCode($codeLength = 6)
    {
        $max = pow(10, $codeLength);
        $min = $max / 10 - 1;
        $code = mt_rand($min, $max);
        return $code;
    }

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['code'])) {
            $attributes['code'] = $this->generateCode();
        }
        parent::__construct($attributes);
    }

    public function send_code()
    {
        if (!$this->user) {
            throw new \Exception("No user attached to this token.");
        }
        if (!$this->code) {
            $this->code = $this->generateCode();
        }

        try {
            $user = User::findOrFail($this->user_id);
            $this->SendCode($user);

        } catch (\Exception $ex) {
            return false;
        }
    }

    //

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
