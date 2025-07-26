jQuery(document).ready(function($) {
    // This will handle all buttons with class 'image-upload'
    $(document).on('click', '.image-upload', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $input = $button.siblings('input[type="hidden"]');
        var previewContainerId = $input.attr('id') + '-preview';
        var $previewContainer = $('#' + previewContainerId);
        var inputName = $input.attr('name');
        var isMultiple = $input.attr('multiple') || inputName.endsWith('[]');
        
        // Supported image extensions
        var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Create media uploader or reuse existing one
        var mediaUploader = wp.media.frames.file_frame || wp.media({
            title: 'Choose Images',
            button: {
                text: 'Choose Images'
            },
            multiple: isMultiple,
            library: {
                type: 'image'
            },
            uploader: {
                filters: {
                    mime_types: [{
                        title: "Image Files",
                        extensions: allowedExtensions.join(',')
                    }]
                }
            }
        });

        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').map(function(attachment) {
                attachment.toJSON();
                return attachment;
            });
            
            // Clear existing preview if not multiple
            if (!isMultiple) {
                $previewContainer.empty();
            }
            
            attachments.forEach(function(attachment) {
                // Double check the file type
                var fileUrl = attachment.attributes.url.toLowerCase();
                var isValid = allowedExtensions.some(function(ext) {
                    return fileUrl.endsWith('.' + ext);
                });
                
                if (!isValid) {
                    alert('Only image files are allowed (jpg, jpeg, png, gif, webp)');
                    return;
                }
                
                // Create preview with remove button
                var item = $('<div class="image-preview-item"></div>');
                item.append('<img src="' + attachment.attributes.url + '" style="max-width: 150px;">');
                item.append('<input type="hidden" name="' + inputName + '" value="' + attachment.id + '">');
                item.append('<a href="#" class="remove-image" title="Remove image"><span class="dashicons dashicons-trash"></span></a>');
                
                $previewContainer.append(item);
            });
        });

        mediaUploader.on('uploader:error', function(error) {
            alert('Error uploading file: ' + error.message);
        });

        mediaUploader.open();
    });

    // Remove image handler
    $(document).on('click', '.remove-image', function(e) {
        e.preventDefault();
        $(this).closest('.image-preview-item').remove();
    });
});