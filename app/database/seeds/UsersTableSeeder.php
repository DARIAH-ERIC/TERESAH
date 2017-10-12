 <?php

class UsersTableSeeder extends Seeder
{ 
    public function run()
    {
        $users = array(
            array(
                "email_address" => "yoann.moranville@dariah.eu",
                "password" => "password",
                # "password_confirmation" => "password",
                "name" => "TERESAH Admin",
                "locale" => "en",
                "active" => true,
                "user_level" => 4,
                "created_at" => new DateTime,
                "updated_at" => new DateTime
            ),
        );

        DB::table("users")->delete();

        foreach ($users as $user) {
            Signup::create($user);
        }
    }
}
