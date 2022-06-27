@props(['comment'])

<x-panel class="bg-gray-50">
    <article class="flex space-x-4">
        <div class="flex-shrink-0">
            <img src="https://i.pravatar.cc/60?u={{ $comment->user_id }}" alt="" width="60" height="60" class="rounded-xl">
        </div>

        <div>
            <header class="mb-4">
                <h3 class="font-bold">{{ $comment->author->username }}</h3>

                <p class="text-xs">
                    Posted
                    <time>{{ $comment->created_at->format('F j, Y, g:i a') }}</time>
                </p>
            </header>

            @if ( $comment->author->assertIsAdmin() || $comment->assertIsCanBeDeleted() )
                <form method="POST" action="{{ route('post.comment.delete', [$comment->post->slug, $comment->id]) }}">
                    @csrf
                    @method('delete')
                    <button type="submit">Удалить</button>
                </form>
            @endif

            <p>
                {{ $comment->body }}
            </p>
        </div>
    </article>
</x-panel>
