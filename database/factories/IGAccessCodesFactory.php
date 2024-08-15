<?php

namespace Database\Factories;

use App\Models\IGAccessCodes;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IGAccessCodes>
 */
class IGAccessCodesFactory extends Factory
{
    protected $model = IGAccessCodes::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'IG_APP_SCOPED_ID' => '8001219773266174',
            'IG_USERNAME' => "systemssavedme",
            'short_lived_access_token' => "IGQWRNcXk0M1JJZAFN1N2VGdDZAZAWGlXRGJ4RVNfdUJPVmgwNW94OGxiVFF0N2RuMUw5WTFfczR3RFZAQRTJJeGljSDdOLVE4UmFBcVVOeVNOaTJVS3U5bjZAiRTlQYzdYWDdoYV9jeUMxWl9CaTlOR3ZAhTEt5UnRXWVBhMjRrREFWODRyZAwZDZD",
            'long_lived_access_token' => "IGQWROb1FmeC1PbXc3a0ZAkZATZAwbzlFQlVDdkI1QlhJMnpZAaUN1TkVIcFpyZA1d1WWVlUUZAZAYW1VLTNzSXVJTEJ6bG5nMmwzQk50VUwtblMxY1l6OUdST1QxdmVvNVNXNDF5WEotczZAlYUpKUQZDZD",
            'long_lived_expires_in' => "5184000",
            'permissions' => "instagram_business_basic,instagram_business_manage_messages,instagram_business_content_publish,instagram_business_manage_comments"
        ];
    }
}
