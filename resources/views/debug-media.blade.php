<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Media URLs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Debug Media URLs</h1>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Posts with Media</h2>
            @php
                $posts = \App\Models\Post::whereNotNull('media_path')->get();
            @endphp
            
            @if($posts->count() > 0)
                @foreach($posts as $post)
                <div class="border rounded p-4 mb-4">
                    <h3 class="font-semibold">Post ID: {{ $post->id }}</h3>
                    <p><strong>Content:</strong> {{ $post->content }}</p>
                    <p><strong>Media Path:</strong> {{ $post->media_path }}</p>
                    <p><strong>Media Type:</strong> {{ $post->media_type }}</p>
                    <p><strong>Generated URL:</strong> {{ $post->media_url }}</p>
                    
                    <div class="mt-4">
                        <h4 class="font-semibold">Direct Image Test:</h4>
                        @if($post->media_type === 'video')
                            <video controls class="w-64 h-48 object-cover rounded">
                                <source src="{{ $post->media_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ $post->media_url }}" alt="Post media" class="w-64 h-48 object-cover rounded">
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="font-semibold">Raw URL Test:</h4>
                        <p>Direct link: <a href="{{ $post->media_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $post->media_url }}</a></p>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-gray-600">No posts with media found.</p>
            @endif
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Storage Configuration</h2>
            <div class="bg-gray-50 p-4 rounded">
                <p><strong>APP_URL:</strong> {{ config('app.url') }}</p>
                <p><strong>Storage URL:</strong> {{ Storage::disk('public')->url('test.jpg') }}</p>
                <p><strong>Storage Path:</strong> {{ Storage::disk('public')->path('') }}</p>
            </div>
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Direct File Test</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded p-4">
                    <h3 class="font-semibold mb-2">Image 1 (Laravel Route)</h3>
                    <img src="/media/posts/1752548577_WhatsApp Image 2025-07-14 at 13.18.11_a6ecc6b1.jpg" 
                         alt="Test Image 1" class="w-full h-32 object-cover rounded">
                    <p class="text-sm text-gray-600 mt-2">WhatsApp Image</p>
                </div>
                <div class="border rounded p-4">
                    <h3 class="font-semibold mb-2">Image 2 (Laravel Route)</h3>
                    <img src="/media/posts/1754130087_go-green-eco-friendly-tips-template-design-b370d5e4c4451b72ef8f38efe1714114_screen.jpg" 
                         alt="Test Image 2" class="w-full h-32 object-cover rounded">
                    <p class="text-sm text-gray-600 mt-2">Green Tips Template</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 