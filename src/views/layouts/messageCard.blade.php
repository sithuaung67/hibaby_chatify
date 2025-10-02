<?php
$seenIcon = !!$seen ? 'check-double' : 'check';
$timeAndSeen =
    "<span data-time='$created_at' class='message-time'>
        " .
    ($sent_by == 'admin' ? "<span class='fas fa-$seenIcon' seen'></span>" : '') .
    " <span class='time'>$timeAgo</span>
    </span>";

$audioId = substr(md5(uniqid(mt_rand(), true)), 0, 8);
$messenger_color = App\Models\User::first()->messenger_color;
$senderColor = $messenger_color ? $messenger_color : Chatify::getFallbackColor();
?>
<div class="message-card @if ($sent_by == 'admin') mc-sender @endif" data-id="{{ $id }}">
    {{-- Delete Message Button --}}
    @if ($sent_by == 'admin')
        <div class="actions">
            <i class="fas fa-trash delete-btn" data-id="{{ $id }}"></i>
        </div>
    @endif

    {{-- Card --}}
    <div class="message-card-content">
        {{-- Normal text or file message --}}
        @if (!in_array(@$attachment->type, ['image', 'audio']) || $message)
            <div class="message">
                {!! $message == null && $attachment != null && @$attachment->type != 'file'
                    ? $attachment->title
                    : nl2br($message) !!}
                {!! $timeAndSeen !!}

                {{-- If attachment is a file --}}
                @if (@$attachment->type == 'file')
                    <a href="{{ route(config('chatify.attachments.download_route_name'), ['fileName' => $attachment->file]) }}"
                       class="file-download">
                        <span class="fas fa-file"></span> {{ $attachment->title }}
                    </a>
                @endif
            </div>
        @endif

        {{-- Image attachment --}}
        @if (@$attachment->type == 'image')
            <div class="image-wrapper" style="text-align: {{ $sent_by == 'admin' ? 'end' : 'start' }}">
                <div class="image-file chat-image"
                     style="background-image: url('{{ Chatify::getAttachmentUrl($attachment->file) }}')">
                    <div>{{ $attachment->title }}</div>
                </div>
                <div style="margin-bottom:5px">
                    {!! $timeAndSeen !!}
                </div>
            </div>
        @endif

        {{-- Audio attachment --}}
        @if (@$attachment->type == 'audio')
            <div class="message">
                <div style="margin-bottom:5px">{!! $timeAndSeen !!}</div>
                <div class="audio-container">
                    <i class="fas fa-play"
                       style="color: {{ $sent_by == 'admin' ? 'white' : $senderColor }}"
                       id="playBtn{{ $audioId }}"
                       onclick="playAudio(this)" data-audio="{{ $audioId }}"></i>
                    <div id="waveform{{ $audioId }}" style="width: 100%; height: 40px;"></div>
                </div>
            </div>
        @endif

        @if (!empty($product_id) && $product = \App\Models\Product::find($product_id))
            @php
                $mainImage = $product->images->first(); 
                $imageUrl = $mainImage ? $mainImage->path : asset('images/no-image.png'); 
            @endphp

            <div class="flex">
                <a href="{{ route('products.show', $product->id) }}" target="_blank" class="product-reply">
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="product-thumb-wide">
                </a>
                <p class="product-name">{{ $product->name }}</p>
            </div>
        @endif


    </div>
</div>

@if ($attachment->type == 'audio')
    <script>
        if (!audioVar) {
            var audioVar = {};
        }
        audioVar["{{ $audioId }}"] = WaveSurfer.create({
            container: '#waveform{{ $audioId }}',
            waveColor: "{{ $sent_by == 'admin' ? '#ffffff' : $senderColor }}",
            progressColor: '#4da8f9',
            height: 40,
            cursorWidth: 0,
            barWidth: 4,
            barGap: 3,
            barRadius: 5,
            url: "{{ Chatify::getAttachmentUrl($attachment->file) }}"
        });

        function playAudio(ele) {
            if (ele.classList.contains('fa-play')) {
                ele.classList.remove('fa-play')
                ele.classList.add('fa-pause')

                audioVar[ele.dataset.audio].play();

                audioVar[ele.dataset.audio].on('finish', () => {
                    let btn = document.getElementById(`playBtn${ele.dataset.audio}`)
                    btn.classList.remove('fa-pause');
                    btn.classList.add('fa-play');
                })
            } else {
                ele.classList.add('fa-play')
                ele.classList.remove('fa-pause')
                audioVar[ele.dataset.audio].pause()
            }
        }
    </script>
@endif

<style>
.product-info {
    margin: 8px 0;
    display: flex;
    align-items: center;
}
.product-card {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fa;
    padding: 6px 10px;
    border-radius: 8px;
}
.product-thumb {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    object-fit: cover;
}
.product-details {
    font-size: 14px;
    color: #333;
}
.product-link {
    text-decoration: none;
    color: inherit;
}
</style>
