<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Faker\Provider\DateTime;


class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('ja_JP');

        $file = $this->faker->image();
        $fileName = basename($file);
        Storage::putFileAs('images/posts', $file, $fileName);
        File::delete($file);

        return [
            'title' => $faker->word(),
            'image' => $fileName,
            'category_id' => Arr::random(Arr::pluck(Category::all(), 'id')),
            'user_id' => Arr::random(Arr::pluck(User::all(), 'id')),
            'body' => $faker->paragraph(),
            'created_at' => DateTime::dateTimeThisDecade(),
        ];
    }
}
