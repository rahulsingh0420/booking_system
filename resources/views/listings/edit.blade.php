<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Listing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('renter.listings.update', $listing) }}" method="POST" enctype="multipart/form-data" id="editListingForm">
                        @csrf
                        @method('PUT')

                        <!-- Hidden container for delete_images -->
                        <div id="delete_images_container"></div>

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $listing->title) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      required>{{ old('description', $listing->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Price per Day</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $listing->price) }}" step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $listing->location) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>
                            
                            <!-- Banner Image Upload -->
                            <div class="mb-6">
                                <label for="banner_image" class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                                <label for="banner_image" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors duration-200 cursor-pointer">
                                    <div class="space-y-1 text-center">
                                        <div id="banner-preview" class="{{ $listing->banner_image ? '' : 'hidden' }} mb-4">
                                            <img src="{{ $listing->banner_image ? Storage::url($listing->banner_image) : '' }}" alt="Banner Preview" class="mx-auto h-48 w-auto rounded-lg shadow-md object-cover">
                                            <button type="button" onclick="removeBannerImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                                Remove Image
                                            </button>
                                        </div>
                                        <div id="banner-upload-placeholder" class="{{ $listing->banner_image ? 'hidden' : '' }} flex flex-col items-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="banner_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Upload a banner image</span>
                                                    <input id="banner_image" name="banner_image" type="file" class="sr-only" accept="image/*" onchange="previewBannerImage(this)">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                </label>
                                @error('banner_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Additional Images Upload -->
                            <div>
                                <label for="additional_images" class="block text-sm font-medium text-gray-700 mb-2">Additional Images (Max 3)</label>
                                <label for="additional_images" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors duration-200 cursor-pointer">
                                    <div class="space-y-1 text-center w-full">
                                        <div id="additional-images-preview" class="grid grid-cols-3 gap-4 mb-4">
                                            @foreach($listing->images as $image)
                                                <div class="relative group existing-image">
                                                    <div class="relative aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Preview" class="w-full h-32 object-cover">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity duration-200">
                                                            <button type="button" 
                                                                    onclick="removeExistingImage({{ $image->id }})" 
                                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                                ×
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="additional-upload-placeholder" class="flex flex-col items-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <span class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    Upload additional images
                                                </span>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                                        </div>
                                    </div>
                                    <input id="additional_images" name="additional_images[]" type="file" class="hidden" accept="image/*" multiple onchange="handleAdditionalImages(this.files)">
                                </label>
                                @error('additional_images')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Selected Location</label>
                            <p id="selected-address" class="mt-1 text-sm text-gray-600">{{ $listing->formatted_address }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Select Location on Map</label>
                            <div id="map" class="mt-1 h-96 w-full rounded-lg border border-gray-300"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $listing->latitude) }}" required>
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $listing->longitude) }}" required>
                            <input type="hidden" name="formatted_address" id="formatted_address" value="{{ old('formatted_address', $listing->formatted_address) }}">
                            @error('latitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('longitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Listing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let marker;
        const defaultLocation = [{{ $listing->latitude }}, {{ $listing->longitude }}];
        let currentAdditionalImages = {{ $listing->images->count() }};
        let additionalImagesFiles = new DataTransfer();
        let existingImages = new Set({{ $listing->images->pluck('id') }});
        let deletedImages = new Set();

        // Banner Image Upload Functions
        function setupBannerUpload() {
            const dropZone = document.querySelector('#banner-upload-placeholder').parentElement.parentElement;
            const input = document.getElementById('banner_image');

            // Handle drag and drop
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-blue-500');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500');
                
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    previewBannerImage(input);
                }
            });
        }

        function previewBannerImage(input) {
            const preview = document.getElementById('banner-preview');
            const placeholder = document.getElementById('banner-upload-placeholder');
            const previewImg = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeBannerImage() {
            const input = document.getElementById('banner_image');
            const preview = document.getElementById('banner-preview');
            const placeholder = document.getElementById('banner-upload-placeholder');
            
            input.value = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');

            // Add hidden input to indicate banner image should be removed
            const removeInput = document.createElement('input');
            removeInput.type = 'hidden';
            removeInput.name = 'remove_banner_image';
            removeInput.value = '1';
            document.querySelector('form').appendChild(removeInput);
        }

        // Additional Images Upload Functions
        function setupAdditionalUpload() {
            const dropZone = document.querySelector('#additional-upload-placeholder').parentElement.parentElement;

            // Handle drag and drop
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-blue-500');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500');
                
                if (e.dataTransfer.files.length) {
                    handleAdditionalImages(e.dataTransfer.files);
                }
            });
        }

        function handleAdditionalImages(files) {
            const remainingSlots = 3 - currentAdditionalImages;
            if (remainingSlots <= 0) {
                alert('You can only upload up to 3 additional images');
                return;
            }

            const filesToAdd = Array.from(files).slice(0, remainingSlots);
            
            // Add new files to our DataTransfer object
            filesToAdd.forEach(file => {
                additionalImagesFiles.items.add(file);
            });

            // Update the input's files
            const input = document.getElementById('additional_images');
            input.files = additionalImagesFiles.files;
            
            currentAdditionalImages += filesToAdd.length;
            previewAdditionalImages();
        }

        function previewAdditionalImages() {
            const preview = document.getElementById('additional-images-preview');
            const placeholder = document.getElementById('additional-upload-placeholder');
            
            // Clear only the new image previews, keep existing ones
            const existingPreviews = preview.querySelectorAll('.existing-image');
            const newPreviews = preview.querySelectorAll('.new-image');
            newPreviews.forEach(preview => preview.remove());
            
            if (additionalImagesFiles.files.length > 0) {
                preview.classList.remove('hidden');
                if (currentAdditionalImages >= 3) {
                    placeholder.classList.add('hidden');
                }
                
                for (let i = 0; i < additionalImagesFiles.files.length; i++) {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative group new-image';
                    
                    reader.onload = function(e) {
                        div.innerHTML = `
                            <div class="relative aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                                <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity duration-200">
                                    <button type="button" 
                                            onclick="removeAdditionalImage(${i})" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        ×
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                    
                    reader.readAsDataURL(additionalImagesFiles.files[i]);
                    preview.appendChild(div);
                }
            } else {
                if (existingPreviews.length === 0) {
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            }
        }

        function removeAdditionalImage(index) {
            const dt = new DataTransfer();
            
            // Add all files except the one being removed
            for (let i = 0; i < additionalImagesFiles.files.length; i++) {
                if (i !== index) {
                    dt.items.add(additionalImagesFiles.files[i]);
                }
            }
            
            additionalImagesFiles = dt;
            currentAdditionalImages--;
            
            // Update the input's files
            const input = document.getElementById('additional_images');
            input.files = additionalImagesFiles.files;
            
            previewAdditionalImages();
        }

        function removeExistingImage(imageId) {
            console.log('Removing image:', imageId);
            
            // Add to deleted images set
            deletedImages.add(imageId);
            
            // Add the image ID to the delete_images array
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_images[]';
            deleteInput.value = imageId;
            document.getElementById('delete_images_container').appendChild(deleteInput);
            
            // Remove the image element from the preview
            const imageElement = document.querySelector(`[onclick="removeExistingImage(${imageId})"]`).closest('.relative');
            if (imageElement) {
                imageElement.remove();
            }
            
            // Update the counter
            currentAdditionalImages--;
            
            // Show the upload placeholder if we're below the limit
            if (currentAdditionalImages < 3) {
                const placeholder = document.getElementById('additional-upload-placeholder');
                if (placeholder) {
                    placeholder.classList.remove('hidden');
                }
            }

            // Log the current state of delete_images inputs
            const deleteInputs = document.querySelectorAll('input[name="delete_images[]"]');
            console.log('Current delete_images:', Array.from(deleteInputs).map(input => input.value));
        }

        // Form submission handler
        document.getElementById('editListingForm').addEventListener('submit', function(e) {
            // Ensure all deleted images are included in the form
            deletedImages.forEach(imageId => {
                if (!document.querySelector(`input[name="delete_images[]"][value="${imageId}"]`)) {
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_images[]';
                    deleteInput.value = imageId;
                    document.getElementById('delete_images_container').appendChild(deleteInput);
                }
            });
        });

        // Map Functions
        async function reverseGeocode(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                return data.display_name;
            } catch (error) {
                console.error('Error getting address:', error);
                return 'Address not found';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Setup image uploads
            setupBannerUpload();
            setupAdditionalUpload();

            // Add existing-image class to all existing image previews
            document.querySelectorAll('#additional-images-preview .relative').forEach(preview => {
                preview.classList.add('existing-image');
            });

            // Initialize map
            map = L.map('map').setView(defaultLocation, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Initialize marker
            marker = L.marker(defaultLocation, {
                draggable: true
            }).addTo(map);

            // Update hidden inputs and address when marker is dragged
            marker.on('dragend', async function(event) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat;
                document.getElementById('longitude').value = position.lng;
                
                const address = await reverseGeocode(position.lat, position.lng);
                document.getElementById('formatted_address').value = address;
                document.getElementById('selected-address').textContent = address;
            });

            // Update marker and address when map is clicked
            map.on('click', async function(event) {
                marker.setLatLng(event.latlng);
                document.getElementById('latitude').value = event.latlng.lat;
                document.getElementById('longitude').value = event.latlng.lng;
                
                const address = await reverseGeocode(event.latlng.lat, event.latlng.lng);
                document.getElementById('formatted_address').value = address;
                document.getElementById('selected-address').textContent = address;
            });
        });
    </script>
    @endpush
</x-app-layout> 