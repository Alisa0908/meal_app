<script type="text/javascript">
    $(function() {
        $('a.disabled').on('click', function() {
            $('a.disabled').prop('disabled', true);
        });
    });
</script>

<x-app-layout>
    <div class="container lg:w-3/4 md:w-4/5 w-11/12 mx-auto my-8 px-8 py-4 bg-white shadow-md">

        <x-flash-message :message="session('notice')" />

        <article class="mb-2">
            <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">
                {{ $post->title }}</h2>
            <h3>{{ $post->user->name }}</h3>
            <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                現在時刻: <span class="text-red-400 font-bold">{{ date('Y-m-d H:i:s') }}</span>
            </p>
            <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                記事作成日: {{ $passed }}
            </p>
            <img src="{{ $post->image_url }}" alt="alt" class="mb-4">
            <p class="text-gray-700 text-base">{!! nl2br(e($post->body)) !!}</p>
        </article>

        {{-- お気に入り登録 --}}
        <div>
            @auth
            @if ($like)
            <form action="{{ route('posts.likes.destroy', [$post, $like]) }}" method="post">
                @csrf
                @method('delete')
                <input type="submit" value="お気に入り削除"
                    class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-40 mr-2">
            </form>
            @else
            <form action="{{ route('posts.likes.store', $post) }}" method="post">
                @csrf
                <input type="submit" value="お気に入り登録"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-40 mr-2">
                    </form>
            @endif
            @endauth
            <p class="font-bold text-base mt-2">お気に入り数: {{ $post->likes->count() }}</p>
        </div>
        {{-- 編集 & 削除 --}}
        <div class="flex flex-row text-center my-4">
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 mr-2">編集</a>
            @endcan
            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="削除" onclick="if(!confirm('削除しますか？')){return false};"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                </form>
            @endcan
        </div>
    </div>
</x-app-layout>
