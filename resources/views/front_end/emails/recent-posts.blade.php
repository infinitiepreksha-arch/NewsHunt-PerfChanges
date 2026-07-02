<!DOCTYPE html>
<html>

<head>
    <style>
        /* Modern container styling with subtle shadow and gradient */
        .add_css {
            max-width: 600px;
            margin: 0 auto;
            border: none;
            padding: 20px;
            border-radius: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* Header content with vibrant gradient and modern typography */
        .html_content {
            margin: 20px;
            text-align: center;
            background: linear-gradient(45deg, #d32f2f, #b71c1c);
            color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.2em;
            line-height: 1.5;
        }

        /* Post container with smooth hover effect and flexbox layout */
        .complete_Content {
            background: #ffffff;
            border-radius: 15px;
            margin: 20px;
            padding: 20px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Flexbox layout for side-by-side arrangement */
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .complete_Content:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        /* Image container with fixed width */
        .image-container {
            flex-shrink: 0;
            width: 150px;
        }

        /* Image styling with modern border and hover zoom */
        .image_css {
            border-radius: 12px;
            border: none;
            padding: 8px;
            background: #f8f9fa;
            width: 117%;
            height: 196px;
            transition: transform 0.3s ease;
            object-fit: cover;
        }

        .image_css:hover {
            transform: scale(1.05);
        }

        /* Content container that takes remaining space */
        .content-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
        }

        .post-details {
            margin-left: 37px;
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .post-details h4 {
            margin: 0 0 10px;
            font-size: 1.3em;
            color: #333;
            line-height: 1.3;
        }

        .post-details p {
            margin: 10px 0;
            color: #555;
            line-height: 1.6;
        }

        /* Read more button with modern styling and hover effect */
        .read_more {
            text-decoration: none;
            background: linear-gradient(45deg, #d32f2f, #b71c1c);
            border: none;
            padding: 10px 20px;
            color: #ffffff;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
            display: inline-block;
            align-self: flex-start;
            margin-top: 10px;
        }

        .anchor_link {
            text-decoration: none;
        }

        .read_more:hover {
            background: linear-gradient(45deg, #b71c1c, #d32f2f);
            transform: translateY(-2px);
        }

        /* Footer text styling */
        .add_css>p {
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #444;
            font-size: 1em;
            margin-top: 20px;
        }

        /* Responsive design for mobile */
        @media (max-width: 480px) {
            .complete_Content {
                flex-direction: column;
            }

            .image-container {
                width: 100%;
            }

            .content-container {
                min-height: auto;
            }
        }
    </style>
</head>

<body>

    <!-- Demo content since we can't use PHP in this example -->
    <div style="max-width: 600px; margin: 0 auto;" class="add_css">
        <div class="html_content">
            <h2>Welcome to {{ $appName }}!</h2>
            <p>Here are your top recent posts:</p>
        </div>

        @foreach ($posts as $post)
            <a class="anchor_link" href="{{ url('/posts/' . $post->slug) }}" target="_blank">
                <div class="post post_data complete_Content">
                    <div class="image-container">
                        <img src="{{ $post->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                            class="image_css" alt="{{ $post->title }}" loading="lazy" >
                    </div>
                    <div class="content-container">
                        <div class="post-details">
                            <h4>{{ $post->title }}</h4>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->description ?? ''), 80) }}</p>
                            <a class="read_more" href="{{ url('/posts/' . $post->slug) }}" target="_blank">Read more
                                →</a>
                            <p><small>Published: {{ $post->pubdate }}</small></p>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
        <p>Thank you for subscribing to {{ $appName }}!</p>
    </div>
</body>

</html>
