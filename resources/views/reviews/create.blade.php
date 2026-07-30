@extends('layouts.app')
@section('content')

<div class="max-w-xl mx-auto px-6 py-20">
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10">
        <h1 class="text-2xl font-black mb-1">Beri Ulasan</h1>
        <p class="text-slate-500 text-sm mb-8">{{ $event->title }} &middot; {{ $event->organization->name ?? '' }}</p>

        <form action="{{ route('reviews.store', $event->id) }}" method="POST">
            @csrf

            <label class="block text-xs font-bold text-slate-500 mb-3 uppercase tracking-widest">Rating Anda</label>
            <div class="flex gap-2 mb-8" id="star-picker">
                @for ($i = 1; $i <= 5; $i++)
                <label class="cursor-pointer">
                    <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" {{ old('rating') == $i ? 'checked' : '' }} required>
                    <span class="star text-4xl text-slate-200 peer-checked:text-amber-400 select-none" data-value="{{ $i }}">★</span>
                </label>
                @endfor
            </div>
            @error('rating') <p class="text-red-500 text-sm mb-4">{{ $message }}</p> @enderror

            <label class="block text-xs font-bold text-slate-500 mb-3 uppercase tracking-widest">Testimoni (Opsional)</label>
            <textarea name="comment" rows="4" placeholder="Bagaimana pengalaman Anda di acara ini?"
                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium mb-8">{{ old('comment') }}</textarea>

            <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                Kirim Ulasan
            </button>
        </form>
    </div>
</div>

<script>
    // Highlight bintang 1..n saat salah satu dipilih (fallback simple, tanpa Alpine)
    document.querySelectorAll('#star-picker input[type=radio]').forEach(radio => {
        radio.addEventListener('change', () => {
            const stars = document.querySelectorAll('#star-picker .star');
            stars.forEach(star => {
                star.classList.toggle('text-amber-400', parseInt(star.dataset.value) <= parseInt(radio.value));
                star.classList.toggle('text-slate-200', parseInt(star.dataset.value) > parseInt(radio.value));
            });
        });
    });
</script>
@endsection
