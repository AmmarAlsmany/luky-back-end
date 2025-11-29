/**
 * Ad Banners Management System
 * Interactive banner creation with live preview, resizing, and positioning
 */

class AdBannerManager {
    constructor() {
        this.selectedBanner = null;
        this.previewElement = null;
        this.isResizing = false;
        this.isDragging = false;
        this.isCreating = false; // Prevent double submission
        this.resizeHandle = null;
        this.dragStart = { x: 0, y: 0 };
        this.resizeStart = { x: 0, y: 0, width: 0, height: 0 };
        this.editMode = false; // Track if we're editing an existing banner
        this.editingBannerId = null; // Store the ID of the banner being edited

        this.init();
    }

    init() {
        this.setupElements();
        this.setupEventListeners();
        this.setupPreviewArea();
        this.setupDateValidation();
        this.selectDefaultBanner();
    }

    setupElements() {
        // Form elements
        this.bannerOptions = document.querySelectorAll('.banner-option');
        this.bannerPreview = document.getElementById('banner-preview');
        this.selectedBannerInput = document.getElementById('selected_banner');
        this.mainTitleInput = document.getElementById('main_title');
        this.titleColorInput = document.getElementById('title_color');
        this.titleFontInput = document.getElementById('title_font');
        this.titleSizeInput = document.getElementById('title_size');
        this.resetTitleColorBtn = document.getElementById('reset_title_color');
        this.providerNameInput = document.getElementById('provider_name');
        this.providerColorInput = document.getElementById('provider_color');
        this.providerFontInput = document.getElementById('provider_font');
        this.providerSizeInput = document.getElementById('provider_size');
        this.resetProviderColorBtn = document.getElementById('reset_provider_color');
        this.offerTextInput = document.getElementById('offer_text');
        this.offerBgColorInput = document.getElementById('offer_bg_color');
        this.offerTextColorInput = document.getElementById('offer_text_color');
        this.offerFontInput = document.getElementById('offer_font');
        this.offerSizeInput = document.getElementById('offer_size');
        this.resetOfferBgColorBtn = document.getElementById('reset_offer_bg_color');
        this.resetOfferTextColorBtn = document.getElementById('reset_offer_text_color');
        this.startDateInput = document.getElementById('start_date');
        this.endDateInput = document.getElementById('end_date');
        
        // Modal elements
        this.previewModal = document.getElementById('previewModal');
        this.modalPreviewContainer = document.getElementById('modal-preview-container');
        this.modalInstance = null; // Store modal instance
        
        // Store text positions
        this.textPositions = {
            title: { x: 0, y: 0, width: 0, height: 0 },
            provider: { x: 0, y: 0, width: 0, height: 0 },
            offer: { x: 0, y: 0, width: 0, height: 0 }
        };
        
        // Preview elements will be created dynamically
        
        // Buttons
        this.resetBtn = document.getElementById('btn_reset_ad');
        this.createBtn = document.getElementById('btn_create_ad');
        this.previewBtn = document.getElementById('btn_preview_ad');
    }

    setupPreviewArea() {
        if (this.bannerPreview) {
            // Make preview area non-draggable (only text elements are draggable)
            this.bannerPreview.style.position = 'relative';
            this.bannerPreview.style.cursor = 'default';
            this.bannerPreview.style.userSelect = 'none';
        }
    }

    // Banner resize handles removed - only text elements are resizable

    // Banner resize method removed

    // Banner resize methods removed

    // Banner drag functionality removed

    // Banner drag methods removed

    setupEventListeners() {
        // Banner selection
        this.bannerOptions.forEach(option => {
            option.addEventListener('click', () => this.selectBanner(option));
        });

        // Live preview updates
        this.mainTitleInput.addEventListener('input', () => this.updatePreview());
        this.titleColorInput.addEventListener('input', () => this.updatePreview());
        this.titleFontInput.addEventListener('change', () => this.updatePreview());
        this.titleSizeInput.addEventListener('change', () => this.updatePreview());
        if (this.resetTitleColorBtn) this.resetTitleColorBtn.addEventListener('click', () => this.resetTitleColor());
        this.providerNameInput.addEventListener('input', () => this.updatePreview());
        this.providerColorInput.addEventListener('input', () => this.updatePreview());
        this.providerFontInput.addEventListener('change', () => this.updatePreview());
        this.providerSizeInput.addEventListener('change', () => this.updatePreview());
        if (this.resetProviderColorBtn) this.resetProviderColorBtn.addEventListener('click', () => this.resetProviderColor());
        this.offerTextInput.addEventListener('input', () => this.updatePreview());
        this.offerBgColorInput.addEventListener('input', () => this.updatePreview());
        this.offerTextColorInput.addEventListener('input', () => this.updatePreview());
        this.offerFontInput.addEventListener('change', () => this.updatePreview());
        this.offerSizeInput.addEventListener('change', () => this.updatePreview());
        if (this.resetOfferBgColorBtn) this.resetOfferBgColorBtn.addEventListener('click', () => this.resetOfferBgColor());
        if (this.resetOfferTextColorBtn) this.resetOfferTextColorBtn.addEventListener('click', () => this.resetOfferTextColor());

        // Banner preview area is now static (no drag/resize for background)

        // Form buttons
        if (this.resetBtn) this.resetBtn.addEventListener('click', () => this.resetForm());
        if (this.createBtn) this.createBtn.addEventListener('click', () => this.createAd());
        if (this.previewBtn) this.previewBtn.addEventListener('click', () => this.openPreview());
        
        // Download button
        const downloadBtn = document.getElementById('download-preview');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', () => this.downloadBanner());
        }
    }

    showResizeHandles() {
        const handles = this.bannerPreview.querySelectorAll('.resize-handle');
        handles.forEach(handle => {
            handle.style.opacity = '1';
        });
    }

    hideResizeHandles() {
        if (!this.isResizing) {
            const handles = this.bannerPreview.querySelectorAll('.resize-handle');
            handles.forEach(handle => {
                handle.style.opacity = '0';
            });
        }
    }

    selectBanner(option) {
        // Remove active class from all options
        this.bannerOptions.forEach(opt => {
            opt.querySelector('.banner-preview').classList.remove('border-primary', 'border-3');
            opt.querySelector('.banner-preview').classList.add('border');
        });

        // Add active class to selected option
        option.querySelector('.banner-preview').classList.remove('border');
        option.querySelector('.banner-preview').classList.add('border-primary', 'border-3');

        // Set selected banner
        const bannerFile = option.dataset.banner;
        this.selectedBanner = bannerFile;
        this.selectedBannerInput.value = bannerFile;

        // Update preview background
        this.updateBannerBackground(bannerFile);
    }

    updateBannerBackground(bannerFile) {
        this.bannerPreview.style.backgroundImage = `url('/images/banners/${bannerFile}')`;
        this.bannerPreview.style.backgroundSize = 'cover';
        this.bannerPreview.style.backgroundPosition = 'center';
        this.bannerPreview.style.backgroundRepeat = 'no-repeat';
        
        // Banner size stays fixed - only background changes
    }

    updatePreview() {
        // Clear existing preview content
        this.clearPreviewContent();
        
        // Create and add preview content dynamically
        this.createPreviewContent();
    }

    clearPreviewContent() {
        // Remove existing preview elements
        const existingTitle = this.bannerPreview.querySelector('#preview-title');
        const existingProvider = this.bannerPreview.querySelector('#preview-provider');
        const existingOffer = this.bannerPreview.querySelector('#preview-offer');
        
        if (existingTitle) existingTitle.remove();
        if (existingProvider) existingProvider.remove();
        if (existingOffer) existingOffer.remove();
    }

    createPreviewContent() {
        // Create title element
        if (this.mainTitleInput.value.trim()) {
            this.createDraggableTextElement('preview-title', this.mainTitleInput.value, {
                fontSize: this.titleSizeInput.value,
                fontWeight: 'bold',
                color: this.titleColorInput.value,
                fontFamily: this.titleFontInput.value,
                textShadow: '2px 2px 4px rgba(0,0,0,0.7)',
                textAlign: 'center'
            }, 'title');
        }

        // Create provider element
        if (this.providerNameInput.value.trim()) {
            this.createDraggableTextElement('preview-provider', this.providerNameInput.value, {
                fontSize: this.providerSizeInput.value,
                color: this.providerColorInput.value,
                fontFamily: this.providerFontInput.value,
                textShadow: '1px 1px 2px rgba(0,0,0,0.7)',
                textAlign: 'center'
            }, 'provider');
        }

        // Create offer element
        if (this.offerTextInput.value.trim()) {
            this.createDraggableTextElement('preview-offer', this.offerTextInput.value, {
                fontSize: this.offerSizeInput.value,
                padding: '8px 16px',
                borderRadius: '20px',
                fontWeight: 'bold',
                backgroundColor: this.offerBgColorInput.value,
                color: this.offerTextColorInput.value,
                fontFamily: this.offerFontInput.value,
                textAlign: 'center'
            }, 'offer');
        }
    }

    createDraggableTextElement(id, text, styles, positionKey) {
        // Remove existing element if it exists
        const existingElement = this.bannerPreview.querySelector(`#${id}`);
        if (existingElement) {
            existingElement.remove();
        }

        // Create text element
        const textElement = document.createElement('div');
        textElement.id = id;
        textElement.textContent = text;
        textElement.style.position = 'absolute';
        textElement.style.cursor = 'move';
        textElement.style.userSelect = 'none';
        textElement.style.zIndex = '20';
        textElement.style.minWidth = '50px';
        textElement.style.minHeight = '20px';
        textElement.style.border = '2px dashed transparent';
        textElement.style.transition = 'border-color 0.3s ease';

        // Apply custom styles
        Object.assign(textElement.style, styles);

        // Set position (use saved position or center)
        const savedPosition = this.textPositions[positionKey];
        if (savedPosition.x > 0 || savedPosition.y > 0) {
            // Use saved position
            textElement.style.left = `${savedPosition.x}px`;
            textElement.style.top = `${savedPosition.y}px`;
            if (savedPosition.width > 0) textElement.style.width = `${savedPosition.width}px`;
            if (savedPosition.height > 0) textElement.style.height = `${savedPosition.height}px`;
        } else {
            // Set initial position (center of banner)
            const bannerRect = this.bannerPreview.getBoundingClientRect();
            const bannerCenterX = bannerRect.width / 2;
            const bannerCenterY = bannerRect.height / 2;

            // Set a reasonable default width for text centering to work
            textElement.style.width = '300px';
            textElement.style.left = `${bannerCenterX - 150}px`; // Center the 300px wide element
            textElement.style.top = `${bannerCenterY - 20}px`;
        }

        // Add resize handles
        this.addTextResizeHandles(textElement, positionKey);

        // Add drag functionality
        this.setupTextDragFunctionality(textElement, positionKey);

        // Add hover effects
        this.setupTextHoverEffects(textElement);

        // Add to banner
        this.bannerPreview.appendChild(textElement);
    }

    addTextResizeHandles(textElement, positionKey) {
        const handles = ['nw', 'ne', 'sw', 'se'];
        
        handles.forEach(handle => {
            const handleElement = document.createElement('div');
            handleElement.className = `text-resize-handle text-resize-${handle}`;
            handleElement.style.cssText = `
                position: absolute;
                width: 8px;
                height: 8px;
                background: #007bff;
                border: 1px solid white;
                border-radius: 50%;
                cursor: ${handle}-resize;
                z-index: 30;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            
            // Position handles
            switch(handle) {
                case 'nw':
                    handleElement.style.top = '-4px';
                    handleElement.style.left = '-4px';
                    break;
                case 'ne':
                    handleElement.style.top = '-4px';
                    handleElement.style.right = '-4px';
                    break;
                case 'sw':
                    handleElement.style.bottom = '-4px';
                    handleElement.style.left = '-4px';
                    break;
                case 'se':
                    handleElement.style.bottom = '-4px';
                    handleElement.style.right = '-4px';
                    break;
            }
            
            textElement.appendChild(handleElement);
            
            // Add resize functionality
            this.setupTextResizeHandle(handleElement, textElement, handle, positionKey);
        });
    }

    setupTextResizeHandle(handleElement, textElement, direction, positionKey) {
        handleElement.addEventListener('mousedown', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const startX = e.clientX;
            const startY = e.clientY;
            const startWidth = textElement.offsetWidth;
            const startHeight = textElement.offsetHeight;
            const startLeft = textElement.offsetLeft;
            const startTop = textElement.offsetTop;
            
            const handleResize = (e) => {
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                
                let newWidth = startWidth;
                let newHeight = startHeight;
                let newLeft = startLeft;
                let newTop = startTop;
                
                switch(direction) {
                    case 'se':
                        newWidth = Math.max(50, startWidth + deltaX);
                        newHeight = Math.max(20, startHeight + deltaY);
                        break;
                    case 'sw':
                        newWidth = Math.max(50, startWidth - deltaX);
                        newHeight = Math.max(20, startHeight + deltaY);
                        newLeft = startLeft + deltaX;
                        break;
                    case 'ne':
                        newWidth = Math.max(50, startWidth + deltaX);
                        newHeight = Math.max(20, startHeight - deltaY);
                        newTop = startTop + deltaY;
                        break;
                    case 'nw':
                        newWidth = Math.max(50, startWidth - deltaX);
                        newHeight = Math.max(20, startHeight - deltaY);
                        newLeft = startLeft + deltaX;
                        newTop = startTop + deltaY;
                        break;
                }
                
                // Keep within banner bounds
                const bannerRect = this.bannerPreview.getBoundingClientRect();
                const maxLeft = bannerRect.width - newWidth;
                const maxTop = bannerRect.height - newHeight;
                
                newLeft = Math.max(0, Math.min(newLeft, maxLeft));
                newTop = Math.max(0, Math.min(newTop, maxTop));
                
                textElement.style.width = newWidth + 'px';
                textElement.style.height = newHeight + 'px';
                textElement.style.left = newLeft + 'px';
                textElement.style.top = newTop + 'px';
                
                // Save position and size
                this.textPositions[positionKey].x = newLeft;
                this.textPositions[positionKey].y = newTop;
                this.textPositions[positionKey].width = newWidth;
                this.textPositions[positionKey].height = newHeight;
            };
            
            const stopResize = () => {
                document.removeEventListener('mousemove', handleResize);
                document.removeEventListener('mouseup', stopResize);
            };
            
            document.addEventListener('mousemove', handleResize);
            document.addEventListener('mouseup', stopResize);
        });
    }

    setupTextDragFunctionality(textElement, positionKey) {
        textElement.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('text-resize-handle')) return;
            
            const startX = e.clientX - textElement.offsetLeft;
            const startY = e.clientY - textElement.offsetTop;
            
            const handleDrag = (e) => {
                const newX = e.clientX - startX;
                const newY = e.clientY - startY;
                
                // Keep within banner bounds
                const bannerRect = this.bannerPreview.getBoundingClientRect();
                const maxX = bannerRect.width - textElement.offsetWidth;
                const maxY = bannerRect.height - textElement.offsetHeight;
                
                const finalX = Math.max(0, Math.min(newX, maxX));
                const finalY = Math.max(0, Math.min(newY, maxY));
                
                textElement.style.left = finalX + 'px';
                textElement.style.top = finalY + 'px';
                
                // Save position
                this.textPositions[positionKey].x = finalX;
                this.textPositions[positionKey].y = finalY;
            };
            
            const stopDrag = () => {
                document.removeEventListener('mousemove', handleDrag);
                document.removeEventListener('mouseup', stopDrag);
            };
            
            document.addEventListener('mousemove', handleDrag);
            document.addEventListener('mouseup', stopDrag);
        });
    }

    setupTextHoverEffects(textElement) {
        textElement.addEventListener('mouseenter', () => {
            textElement.style.borderColor = '#007bff';
            const handles = textElement.querySelectorAll('.text-resize-handle');
            handles.forEach(handle => {
                handle.style.opacity = '1';
            });
        });
        
        textElement.addEventListener('mouseleave', () => {
            textElement.style.borderColor = 'transparent';
            const handles = textElement.querySelectorAll('.text-resize-handle');
            handles.forEach(handle => {
                handle.style.opacity = '0';
            });
        });
    }

    setupDateValidation() {
        this.startDateInput.addEventListener('change', () => this.validateDates());
        this.endDateInput.addEventListener('change', () => this.validateDates());
    }

    selectDefaultBanner() {
        // Select the first banner (Banner 1) as default
        if (this.bannerOptions.length > 0) {
            this.selectBanner(this.bannerOptions[0]);
        }
    }

    resetProviderColor() {
        this.providerColorInput.value = '#ffffff';
        this.updatePreview();
        this.showToast(window.bannerTranslations?.provider_color_reset || 'Provider color reset to white', 'info');
    }

    resetOfferBgColor() {
        this.offerBgColorInput.value = '#ffc107';
        this.updatePreview();
        this.showToast(window.bannerTranslations?.offer_bg_color_reset || 'Offer background color reset to yellow', 'info');
    }

    resetOfferTextColor() {
        this.offerTextColorInput.value = '#000000';
        this.updatePreview();
        this.showToast(window.bannerTranslations?.offer_text_color_reset || 'Offer text color reset to black', 'info');
    }

    validateDates() {
        if (this.startDateInput.value && this.endDateInput.value) {
            if (new Date(this.startDateInput.value) >= new Date(this.endDateInput.value)) {
                this.endDateInput.setCustomValidity('End date must be after start date');
                this.endDateInput.reportValidity();
            } else {
                this.endDateInput.setCustomValidity('');
            }
        }
    }

    resetForm() {
        document.getElementById('ad-banner-form').reset();
        this.selectedBannerInput.value = '';
        this.selectedBanner = null;

        // Reset edit mode
        this.editMode = false;
        this.editingBannerId = null;

        // Update button text
        this.createBtn.innerHTML = '<iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>Create Ad';

        // Reset all colors, fonts, and sizes to defaults
        this.titleColorInput.value = '#ffffff';
        this.titleFontInput.value = 'Arial, sans-serif';
        this.titleSizeInput.value = '2rem';
        this.providerColorInput.value = '#ffffff';
        this.providerFontInput.value = 'Arial, sans-serif';
        this.providerSizeInput.value = '1.2rem';
        this.offerBgColorInput.value = '#ffc107';
        this.offerTextColorInput.value = '#000000';
        this.offerFontInput.value = 'Arial, sans-serif';
        this.offerSizeInput.value = '1.1rem';

        // Reset text positions
        this.textPositions = {
            title: { x: 0, y: 0, width: 0, height: 0 },
            provider: { x: 0, y: 0, width: 0, height: 0 },
            offer: { x: 0, y: 0, width: 0, height: 0 }
        };

        // Reset banner selection
        this.bannerOptions.forEach(opt => {
            opt.querySelector('.banner-preview').classList.remove('border-primary', 'border-3');
            opt.querySelector('.banner-preview').classList.add('border');
        });

        // Reset preview (banner stays fixed size)
        this.bannerPreview.style.backgroundImage = '';
        this.bannerPreview.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

        // Clear preview content
        this.clearPreviewContent();
    }

    async createAd() {
        // Prevent double submission
        if (this.isCreating) {
            console.log('Already creating banner, ignoring duplicate request');
            return;
        }

        // Log edit mode status for debugging
        console.log('Edit Mode:', this.editMode);
        console.log('Editing Banner ID:', this.editingBannerId);

        // Validate form
        if (!this.mainTitleInput.value || !this.providerNameInput.value ||
            !this.offerTextInput.value || !this.selectedBannerInput.value ||
            !this.startDateInput.value || !this.endDateInput.value) {
            this.showToast(window.bannerTranslations?.fill_required_fields || 'Please fill in all required fields and select a banner background.', 'warning');
            return;
        }

        // Validate date range
        if (new Date(this.startDateInput.value) >= new Date(this.endDateInput.value)) {
            this.showToast(window.bannerTranslations?.end_date_must_be_after || 'End date must be after start date.', 'error');
            return;
        }

        // Set creating flag
        this.isCreating = true;

        // Show loading state
        const loadingText = this.editMode ? 'Updating...' : 'Creating...';
        this.createBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`;
        this.createBtn.disabled = true;

        try {
            // Generate banner image using html2canvas
            const bannerImageData = await this.generateBannerImage();

            if (!bannerImageData) {
                throw new Error('Failed to generate banner image');
            }

            // Prepare data for API
            const bannerData = {
                title: this.mainTitleInput.value,
                provider_name: this.providerNameInput.value,
                offer_text: this.offerTextInput.value,
                banner_template: this.selectedBannerInput.value,
                title_color: this.titleColorInput.value,
                title_font: this.titleFontInput.value,
                title_size: this.titleSizeInput.value,
                provider_color: this.providerColorInput.value,
                provider_font: this.providerFontInput.value,
                provider_size: this.providerSizeInput.value,
                offer_text_color: this.offerTextColorInput.value,
                offer_bg_color: this.offerBgColorInput.value,
                offer_font: this.offerFontInput.value,
                offer_size: this.offerSizeInput.value,
                banner_image: bannerImageData, // Base64 encoded image
                link_url: null,
                start_date: this.startDateInput.value,
                end_date: this.endDateInput.value,
                display_location: 'home',
                display_order: 0
            };

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Determine endpoint and method based on edit mode
            const url = this.editMode ? `/banners/${this.editingBannerId}` : '/banners/store';
            const method = this.editMode ? 'PUT' : 'POST';
            const successMessage = this.editMode ? 'Banner updated successfully!' : 'Banner created successfully!';

            console.log('Request URL:', url);
            console.log('Request Method:', method);

            // Send to web route (not API)
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify(bannerData)
            });

            const result = await response.json();

            if (result.success) {
                this.showToast(successMessage, 'success');

                // Reset form
                this.resetForm();

                // Reload page to show new/updated banner in the list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || (this.editMode ? 'Failed to update banner' : 'Failed to create banner'));
            }
        } catch (error) {
            console.error('Error creating banner:', error);
            this.showToast(error.message || 'Failed to create banner. Please try again.', 'error');
        } finally {
            // Reset creating flag
            this.isCreating = false;
            
            // Reset button
            this.createBtn.innerHTML = '<iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>Create Ad';
            this.createBtn.disabled = false;
        }
    }

    async generateBannerImage() {
        return new Promise((resolve, reject) => {
            if (typeof html2canvas === 'undefined') {
                reject(new Error('html2canvas library not loaded'));
                return;
            }

            // Clone the preview element to avoid modifying the original
            const clonedPreview = this.bannerPreview.cloneNode(true);

            // Remove resize handles and borders from cloned elements
            const textElements = clonedPreview.querySelectorAll('[id^="preview-"]');
            textElements.forEach(el => {
                el.style.border = 'none';
                const handles = el.querySelectorAll('.text-resize-handle');
                handles.forEach(handle => handle.remove());
            });

            // Temporarily add to document for rendering
            clonedPreview.style.position = 'absolute';
            clonedPreview.style.left = '-9999px';
            document.body.appendChild(clonedPreview);

            html2canvas(clonedPreview, {
                backgroundColor: null,
                scale: 2,
                useCORS: true,
                allowTaint: true,
                width: 600,
                height: 300
            }).then(canvas => {
                // Remove cloned element
                document.body.removeChild(clonedPreview);

                // Convert to base64
                const base64Image = canvas.toDataURL('image/png');
                resolve(base64Image);
            }).catch(error => {
                document.body.removeChild(clonedPreview);
                reject(error);
            });
        });
    }

    openPreview() {
        if (!this.selectedBannerInput.value) {
            this.showToast(window.bannerTranslations?.select_banner_first || 'Please select a banner background first.', 'warning');
            return;
        }
        
        // Create modal preview
        this.createModalPreview();
        
        // Dispose of existing modal instance if any
        if (this.modalInstance) {
            this.modalInstance.dispose();
        }
        
        // Create and show new modal instance
        this.modalInstance = new bootstrap.Modal(this.previewModal, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        this.modalInstance.show();
        
        // Clean up on hide
        this.previewModal.addEventListener('hidden.bs.modal', () => {
            if (this.modalInstance) {
                this.modalInstance.dispose();
                this.modalInstance = null;
            }
        }, { once: true });
    }

    createModalPreview() {
        // Clear existing content
        this.modalPreviewContainer.innerHTML = '';
        
        // Get live preview dimensions
        const livePreviewRect = this.bannerPreview.getBoundingClientRect();
        const liveWidth = livePreviewRect.width;
        const liveHeight = livePreviewRect.height;
        
        // Modal dimensions
        const modalWidth = 600;
        const modalHeight = 400;
        
        // Calculate scale factors
        const scaleX = modalWidth / liveWidth;
        const scaleY = modalHeight / liveHeight;
        
        // Create preview container with same aspect ratio
        const previewContainer = document.createElement('div');
        previewContainer.style.cssText = `
            width: ${modalWidth}px;
            height: ${modalHeight}px;
            background-image: url('/images/banners/${this.selectedBannerInput.value}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            margin: 0 auto;
            overflow: hidden;
        `;
        
        // Clone and scale text elements
        const liveTitle = this.bannerPreview.querySelector('#preview-title');
        const liveProvider = this.bannerPreview.querySelector('#preview-provider');
        const liveOffer = this.bannerPreview.querySelector('#preview-offer');
        
        if (liveTitle) {
            const titleElement = this.createScaledTextElement(liveTitle, scaleX, scaleY);
            previewContainer.appendChild(titleElement);
        }
        
        if (liveProvider) {
            const providerElement = this.createScaledTextElement(liveProvider, scaleX, scaleY);
            previewContainer.appendChild(providerElement);
        }
        
        if (liveOffer) {
            const offerElement = this.createScaledTextElement(liveOffer, scaleX, scaleY);
            previewContainer.appendChild(offerElement);
        }
        
        this.modalPreviewContainer.appendChild(previewContainer);
        
        // Store reference for download
        this.modalPreviewElement = previewContainer;
    }

    createScaledTextElement(sourceElement, scaleX, scaleY) {
        const scaledElement = sourceElement.cloneNode(true);
        
        // Get original computed styles
        const computedStyle = window.getComputedStyle(sourceElement);
        const originalLeft = parseFloat(computedStyle.left) || 0;
        const originalTop = parseFloat(computedStyle.top) || 0;
        const originalWidth = parseFloat(computedStyle.width) || sourceElement.offsetWidth;
        const originalHeight = parseFloat(computedStyle.height) || sourceElement.offsetHeight;
        const originalFontSize = parseFloat(computedStyle.fontSize) || 16;
        
        // Calculate scaled positions and sizes
        const scaledLeft = originalLeft * scaleX;
        const scaledTop = originalTop * scaleY;
        const scaledWidth = originalWidth * scaleX;
        const scaledHeight = originalHeight * scaleY;
        const scaledFontSize = originalFontSize * Math.min(scaleX, scaleY);
        
        // Apply scaled styles
        scaledElement.style.cssText = `
            position: absolute;
            left: ${scaledLeft}px;
            top: ${scaledTop}px;
            width: ${scaledWidth}px;
            height: ${scaledHeight}px;
            font-size: ${scaledFontSize}px;
            cursor: default;
            border: none;
            user-select: text;
            pointer-events: none;
            ${computedStyle.color ? `color: ${computedStyle.color};` : ''}
            ${computedStyle.fontFamily ? `font-family: ${computedStyle.fontFamily};` : ''}
            ${computedStyle.fontWeight ? `font-weight: ${computedStyle.fontWeight};` : ''}
            ${computedStyle.textShadow ? `text-shadow: ${computedStyle.textShadow};` : ''}
            ${computedStyle.backgroundColor ? `background-color: ${computedStyle.backgroundColor};` : ''}
            ${computedStyle.padding ? `padding: ${computedStyle.padding};` : ''}
            ${computedStyle.borderRadius ? `border-radius: ${computedStyle.borderRadius};` : ''}
            ${computedStyle.textAlign ? `text-align: ${computedStyle.textAlign};` : ''}
        `;
        
        // Remove resize handles
        const handles = scaledElement.querySelectorAll('.text-resize-handle');
        handles.forEach(handle => handle.remove());
        
        return scaledElement;
    }
        
    downloadBanner() {
        if (!this.modalPreviewElement) {
            this.showToast(window.bannerTranslations?.preview_banner_first || 'Please preview the banner first.', 'warning');
            return;
        }
        
        try {
            // Use html2canvas to convert the banner to image
            if (typeof html2canvas !== 'undefined') {
                html2canvas(this.modalPreviewElement, {
                    backgroundColor: null,
                    scale: 2, // Higher quality
                    useCORS: true,
                    allowTaint: true
                }).then(canvas => {
                    // Create download link
                    const link = document.createElement('a');
                    link.download = `banner-${Date.now()}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();

                    this.showToast(window.bannerTranslations?.banner_downloaded || 'Banner downloaded successfully!', 'success');
                }).catch(error => {
                    console.error('Download error:', error);
                    this.showToast(window.bannerTranslations?.download_failed || 'Download failed. Please try again.', 'error');
                });
            } else {
                // Fallback: Create a simple download using canvas
                this.downloadBannerFallback();
            }
        } catch (error) {
            console.error('Download error:', error);
            this.showToast(window.bannerTranslations?.download_failed || 'Download failed. Please try again.', 'error');
        }
    }

    downloadBannerFallback() {
        // Create a canvas element
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas size
        canvas.width = 600;
        canvas.height = 400;
        
        // Create image element for background
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            // Draw background
            ctx.drawImage(img, 0, 0, 600, 400);
            
            // Draw text elements
            this.drawTextOnCanvas(ctx);
            
            // Download
            const link = document.createElement('a');
            link.download = `banner-${Date.now()}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();

            this.showToast(window.bannerTranslations?.banner_downloaded || 'Banner downloaded successfully!', 'success');
        };
        
        img.src = `/images/banners/${this.selectedBannerInput.value}`;
    }

    drawTextOnCanvas(ctx) {
        // Get live preview dimensions for scaling
        const livePreviewRect = this.bannerPreview.getBoundingClientRect();
        const liveWidth = livePreviewRect.width;
        const liveHeight = livePreviewRect.height;
        
        // Canvas dimensions
        const canvasWidth = 600;
        const canvasHeight = 400;
        
        // Calculate scale factors
        const scaleX = canvasWidth / liveWidth;
        const scaleY = canvasHeight / liveHeight;
        
        // Draw title
        if (this.mainTitleInput.value.trim()) {
            ctx.fillStyle = this.titleColorInput.value;
            ctx.font = `${this.titleSizeInput.value} ${this.titleFontInput.value}`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Get position from saved positions and scale
            const titlePos = this.textPositions.title;
            const x = titlePos.x > 0 ? (titlePos.x + 100) * scaleX : canvasWidth / 2;
            const y = titlePos.y > 0 ? (titlePos.y + 50) * scaleY : canvasHeight / 2 - 50;
            
            ctx.fillText(this.mainTitleInput.value, x, y);
        }
        
        // Draw provider
        if (this.providerNameInput.value.trim()) {
            ctx.fillStyle = this.providerColorInput.value;
            ctx.font = `${this.providerSizeInput.value} ${this.providerFontInput.value}`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            const providerPos = this.textPositions.provider;
            const x = providerPos.x > 0 ? (providerPos.x + 100) * scaleX : canvasWidth / 2;
            const y = providerPos.y > 0 ? (providerPos.y + 50) * scaleY : canvasHeight / 2;
            
            ctx.fillText(this.providerNameInput.value, x, y);
        }
        
        // Draw offer
        if (this.offerTextInput.value.trim()) {
            ctx.fillStyle = this.offerTextColorInput.value;
            ctx.font = `${this.offerSizeInput.value} ${this.offerFontInput.value}`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            const offerPos = this.textPositions.offer;
            const x = offerPos.x > 0 ? (offerPos.x + 100) * scaleX : canvasWidth / 2;
            const y = offerPos.y > 0 ? (offerPos.y + 50) * scaleY : canvasHeight / 2 + 50;
            
            // Draw background for offer
            const textWidth = ctx.measureText(this.offerTextInput.value).width;
            const padding = 20;
            ctx.fillStyle = this.offerBgColorInput.value;
            ctx.fillRect(x - textWidth/2 - padding, y - 20, textWidth + padding*2, 40);
            
            // Draw text
            ctx.fillStyle = this.offerTextColorInput.value;
            ctx.fillText(this.offerTextInput.value, x, y);
        }
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const bgColor = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type];
        
        toast.className = `toast align-items-center text-white ${bgColor} border-0 position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <iconify-icon icon="solar:check-circle-bold-duotone" class="me-2"></iconify-icon>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// Global functions for ad management
async function deleteAd(adId) {
    if (!confirm(window.bannerTranslations?.confirm_delete || 'Are you sure you want to delete this ad? This action cannot be undone.')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(`/banners/${adId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            }
        });

        const result = await response.json();

        if (result.success) {
            const adElement = document.querySelector(`[data-ad-id="${adId}"]`);
            if (adElement) {
                // Add fade out animation
                adElement.style.transition = 'opacity 0.3s ease';
                adElement.style.opacity = '0';

                setTimeout(() => {
                    adElement.remove();

                    // Show success message
                    showToast(window.bannerTranslations?.ad_deleted || 'Ad deleted successfully!', 'success');
                }, 300);
            }
        } else {
            showToast(result.message || (window.bannerTranslations?.delete_failed || 'Failed to delete ad'), 'error');
        }
    } catch (error) {
        console.error('Error deleting ad:', error);
        showToast(window.bannerTranslations?.delete_failed || 'Failed to delete ad. Please try again.', 'error');
    }
}

// Helper function for toasts (can be used globally)
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type];

    toast.className = `toast align-items-center text-white ${bgColor} border-0 position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <iconify-icon icon="solar:check-circle-bold-duotone" class="me-2"></iconify-icon>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);

    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

async function editAd(adId) {
    try {
        // Fetch banner data from API
        const response = await fetch(`/banners/${adId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success && result.data.banner) {
            const banner = result.data.banner;

            // Populate form with banner data
            document.getElementById('main_title').value = banner.title || '';
            document.getElementById('provider_name').value = banner.provider_name || '';
            document.getElementById('offer_text').value = banner.offer_text || '';
            document.getElementById('start_date').value = banner.start_date || '';
            document.getElementById('end_date').value = banner.end_date || '';

            // Populate colors
            document.getElementById('title_color').value = banner.title_color || '#ffffff';
            document.getElementById('provider_color').value = banner.provider_color || '#ffffff';
            document.getElementById('offer_text_color').value = banner.offer_text_color || '#000000';
            document.getElementById('offer_bg_color').value = banner.offer_bg_color || '#ffc107';

            // Populate fonts
            document.getElementById('title_font').value = banner.title_font || 'Arial, sans-serif';
            document.getElementById('provider_font').value = banner.provider_font || 'Arial, sans-serif';
            document.getElementById('offer_font').value = banner.offer_font || 'Arial, sans-serif';

            // Populate sizes
            document.getElementById('title_size').value = banner.title_size || '2rem';
            document.getElementById('provider_size').value = banner.provider_size || '1.2rem';
            document.getElementById('offer_size').value = banner.offer_size || '1.1rem';

            // Select the banner template
            if (banner.banner_template) {
                const bannerOption = document.querySelector(`[data-banner="${banner.banner_template}"]`);
                if (bannerOption) {
                    bannerOption.click();
                }
            }

            // Update preview with new data
            if (window.adBannerManager) {
                // Set edit mode
                window.adBannerManager.editMode = true;
                window.adBannerManager.editingBannerId = adId;

                // Update button text to indicate edit mode
                window.adBannerManager.createBtn.innerHTML = '<iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>Update Banner';

                // Update preview
                window.adBannerManager.updatePreview();
            }

            // Scroll to form
            document.getElementById('create-ad-banner').scrollIntoView({ behavior: 'smooth' });

            // Show edit mode message
            const editingMsg = window.bannerTranslations?.editing_banner || 'Editing banner';
            showToast(`${editingMsg}: ${banner.title}`, 'info');
        } else {
            showToast(window.bannerTranslations?.failed_load_banner || 'Failed to load banner data', 'error');
        }
    } catch (error) {
        console.error('Error loading banner:', error);
        showToast(window.bannerTranslations?.failed_load_banner || 'Failed to load banner data', 'error');
    }
}

// Note: AdBannerManager is initialized in the blade template
// Do not initialize here to avoid duplicate instances
