            {{-- Review Modal --}}
            <div id="review-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 my-8">
                    <h3 class="font-bold text-gray-900 text-xl mb-1">Berikan Ulasan Produk</h3>
                    <p class="text-gray-500 text-sm mb-5">Ulasan Anda membantu pembeli lain membuat keputusan yang tepat</p>
                    
                    <form id="review-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" id="review-product-id">
                        
                        {{-- Product Name Display --}}
                        <div class="mb-5 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-semibold">Produk</p>
                            <p id="review-product-name" class="font-semibold text-gray-800 mt-1"></p>
                        </div>

                        {{-- Rating --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Rating</label>
                            <div class="flex items-center gap-2">
                                <div id="rating-stars" class="flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                            class="rating-star text-4xl cursor-pointer transition" 
                                            data-rating="{{ $i }}">
                                            ☆
                                        </button>
                                    @endfor
                                </div>
                                <span id="rating-value" class="text-lg font-bold text-herbal-700 ml-3">0/5</span>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="0">
                        </div>

                        {{-- Comment --}}
                        <div class="mb-5">
                            <label for="review-comment" class="block text-sm font-semibold text-gray-800 mb-2">Komentar (Opsional)</label>
                            <textarea name="comment" id="review-comment" rows="4" 
                                placeholder="Ceritakan pengalaman Anda menggunakan produk ini..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-herbal-500 focus:border-transparent resize-none"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Maksimal 1000 karakter</p>
                        </div>

                        {{-- Image Upload --}}
                        <div class="mb-6">
                            <label for="review-image" class="block text-sm font-semibold text-gray-800 mb-2">Foto Produk (Opsional)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-herbal-500 hover:bg-herbal-50 transition cursor-pointer"
                                 onclick="document.getElementById('review-image').click()">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-700">Klik untuk upload foto</p>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, atau WebP (Max. 2MB)</p>
                                <input type="file" name="image" id="review-image" class="hidden" accept="image/*">
                            </div>
                            <div id="image-preview" class="mt-3 hidden">
                                <p class="text-xs text-gray-500 mb-2">Preview:</p>
                                <img id="preview-img" src="" alt="Preview" class="max-w-xs h-32 object-cover rounded-lg border border-gray-200">
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-3">
                            <button type="button" onclick="closeReviewModal()"
                                class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 bg-herbal-700 text-white py-3 rounded-xl text-sm font-semibold hover:bg-herbal-800 transition">
                                Kirim Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                let selectedRating = 0;

                // Rating stars interaction
                document.querySelectorAll('.rating-star').forEach(star => {
                    star.addEventListener('click', function() {
                        selectedRating = parseInt(this.dataset.rating);
                        document.getElementById('rating-input').value = selectedRating;
                        updateRatingDisplay();
                    });

                    star.addEventListener('mouseover', function() {
                        const hoverRating = parseInt(this.dataset.rating);
                        document.querySelectorAll('.rating-star').forEach((s, idx) => {
                            if (idx < hoverRating) {
                                s.textContent = '★';
                                s.classList.add('text-yellow-400');
                            } else {
                                s.textContent = '☆';
                                s.classList.remove('text-yellow-400');
                            }
                        });
                    });
                });

                document.getElementById('rating-stars').addEventListener('mouseout', updateRatingDisplay);

                function updateRatingDisplay() {
                    const stars = document.querySelectorAll('.rating-star');
                    stars.forEach((star, idx) => {
                        if (idx < selectedRating) {
                            star.textContent = '★';
                            star.classList.add('text-yellow-400');
                        } else {
                            star.textContent = '☆';
                            star.classList.remove('text-yellow-400');
                        }
                    });
                    document.getElementById('rating-value').textContent = selectedRating + '/5';
                }

                // Image preview
                document.getElementById('review-image').addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            document.getElementById('preview-img').src = event.target.result;
                            document.getElementById('image-preview').classList.remove('hidden');
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Modal functions
                function openReviewModal(productId, productName) {
                    selectedRating = 0;
                    document.getElementById('review-product-id').value = productId;
                    document.getElementById('review-product-name').textContent = productName;
                    document.getElementById('review-form').action = '{{ route("orders.review", $order) }}';
                    document.getElementById('review-modal').classList.remove('hidden');
                    updateRatingDisplay();
                    document.getElementById('review-comment').value = '';
                    document.getElementById('image-preview').classList.add('hidden');
                }

                function closeReviewModal() {
                    document.getElementById('review-modal').classList.add('hidden');
                    document.getElementById('review-form').reset();
                }
            </script>
