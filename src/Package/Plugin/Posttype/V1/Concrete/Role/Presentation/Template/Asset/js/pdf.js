jQuery(document).ready(function($) {
            var mediaUploader;
            var allowedExtensions = ['pdf'];
            var previewContainer = $('#<?php echo $this->id; ?>-preview');
            var fileInput = $('#<?php echo $this->id; ?>');
            
            // Handle click on our custom upload button
            $('#upload-<?php echo $this->id; ?>').click(function(e) {
                e.preventDefault();
                
                // WordPress media library upload
                if (typeof wp !== 'undefined' && wp.media) {
                    this.openMediaUploader();
                    return;
                }
                
                // Fallback to regular file input
                fileInput.trigger('click');
            });
            
            // Handle file selection via regular file input
            fileInput.on('change', function() {
                var files = this.files;
                
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    
                    // Validate file type
                    if (!this.isValidPdf(file)) {
                        alert('Only PDF files are allowed');
                        continue;
                    }
                    
                    // Create preview
                    var previewItem = $('<div class="pdf-preview-item"></div>');
                    previewItem.append('<span class="dashicons dashicons-pdf"></span>');
                    previewItem.append('<span class="pdf-filename">' + file.name + '</span>');
                    previewItem.append('<a href="#" class="view-pdf" target="_blank" title="View PDF" style="display: none;"><span class="dashicons dashicons-visibility"></span></a>');
                    previewItem.append('<a href="#" class="remove-pdf" title="Remove PDF"><span class="dashicons dashicons-trash"></span></a>');
                    
                    previewContainer.append(previewItem);
                }
                
                // Reset input to allow selecting same files again
                $(this).val('');
            });
            
            // Remove PDF handler
            previewContainer.on('click', '.remove-pdf', function(e) {
                e.preventDefault();
                $(this).closest('.pdf-preview-item').remove();
            });
            
            // WordPress media uploader
            this.openMediaUploader = function() {
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose PDF',
                    button: {
                        text: 'Choose PDF'
                    },
                    multiple: 'true',
                    library: {
                        type: 'application/pdf'
                    },
                    uploader: {
                        filters: {
                            mime_types: [
                                {
                                    title: "PDF Files",
                                    extensions: allowedExtensions.join(',')
                                }
                            ]
                        }
                    }
                });

                mediaUploader.on('select', function() {
                    var attachments = mediaUploader.state().get('selection').map(function(attachment) {
                        attachment.toJSON();
                        return attachment;
                    });
                    
                    attachments.forEach(function(attachment) {
                        // Double check the file type
                        var fileUrl = attachment.attributes.url.toLowerCase();
                        var isValid = fileUrl.endsWith('.pdf');
                        
                        if (!isValid) {
                            alert('Only PDF files are allowed');
                            return;
                        }
                        
                        // Create preview with remove button
                        var previewItem = $('<div class="pdf-preview-item"></div>');
                        previewItem.append('<span class="dashicons dashicons-pdf"></span>');
                        previewItem.append('<span class="pdf-filename">' + attachment.attributes.title + '</span>');
                        previewItem.append('<a href="' + attachment.attributes.url + '" target="_blank" class="view-pdf" title="View PDF"><span class="dashicons dashicons-visibility"></span></a>');
                        previewItem.append('<input type="hidden" name="<?php echo $this->name; ?>[]" value="' + attachment.id + '">');
                        previewItem.append('<a href="#" class="remove-pdf" title="Remove PDF"><span class="dashicons dashicons-trash"></span></a>');
                        
                        previewContainer.append(previewItem);
                    });
                });

                mediaUploader.on('uploader:error', function(error) {
                    alert('Error uploading file: ' + error.message);
                });

                mediaUploader.open();
            };
            
            // Helper function to validate PDF files
            this.isValidPdf = function(file) {
                var fileName = file.name.toLowerCase();
                return fileName.endsWith('.pdf');
            };
        });