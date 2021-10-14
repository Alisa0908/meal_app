<x-app-layout>
    <div class="container max-w-7xl mx-auto px-4 md:px-12 pb-3 mt-3">
        <x-flash-message :message="session('notice')" />
        <div class="flex flex-wrap -mx-1 lg:-mx-4 mb-4">
            @foreach ($posts as $post)
                <article class="w-full px-4 md:w-1/2 text-xl text-gray-800 leading-normal">
                    <a href="{{ route('posts.show', $post) }}">
                        <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">
                            {{ $post->title }}</h2>
                        <h3>{{ $post->user->name }}</h3>
                        <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                            現在時刻: <span class="text-red-400 font-bold">{{ date('Y-m-d H:i:s') }}</span>
                        </p>
                        <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                            <?php $create_time = $post->created_at;
                            $today = date('Y-m-d H:i:s');
                            
                            $diff = $create_time->diff($today);
                            if ($diff->format('%h') < 1) {
                                $passed = $diff->format('%i分前');
                            } elseif ($diff->format('%d') < 1) {
                                $passed = $diff->format('%h時間前');
                            } elseif ($diff->format('%m') < 1) {
                                $passed = $diff->format('%d日前');
                            } elseif ($diff->format('%y') < 1) {
                                $passed = $diff->format('%mヵ月前');
                            } else {
                                $passed = $post->created_at->format('Y年m月d日');
                            }
                            ?>
                            記事作成日: {{ $passed }}
                        </p>
                        <img class="w-full mb-2" src="{{ $post->image_url }}" alt="">
                        <p class="text-gray-700 text-base">{{ Str::limit($post->body, 50) }}</p>
                        <p class="text-xl font-bold">お気に入り数 : {{ $post->likes->count() }}</p>
                    </a>
                </article>
            @endforeach
        </div>
        {{ $posts->links() }}
    </div>
</x-app-layout>
