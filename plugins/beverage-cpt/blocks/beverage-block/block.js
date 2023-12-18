const beverageRegisterBlockType = wp.blocks.registerBlockType;

const { 
    RichText: BeverageRichText,
    MediaUpload: BeverageMediaUpload,
    MediaUploadCheck: BeverageMediaUploadCheck
} = wp.blockEditor || wp.editor;

const {
    TextControl: BeverageTextControl,
    Button: BeverageButton,
    Placeholder: BeveragePlaceholder,
    ToggleControl: BeverageToggleControl,
    SelectControl: BeverageSelectControl, // Add SelectControl
} = wp.components;

const beverageEl = wp.element.createElement;

beverageRegisterBlockType('beverage-cpt/beverage-block', {
    title: 'Beverage Block',
    icon: 'beer',
    category: 'common',
    attributes: {
        title: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'beverage_title'
        },
        description: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'beverage_description'
        },
        type: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'beverage_type'
        },
        alcoholContent: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'beverage_alcohol_content'
        },
        imageUrl: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'beverage_image_url'
        },
        availability: {
            type: 'boolean',
            default: true,
            source: 'meta',
            meta: 'beverage_availability'
        },
        position: {
            type: 'integer',
            default: 1, // Set 1 as the default value
            source: 'meta',
            meta: 'beverage_position'
        },
        
    },
    edit: function(props) {
        const { attributes: { title, description, type, alcoholContent, imageUrl, availability, position }, setAttributes } = props;

        function onSelectImage(media) {
            setAttributes({ imageUrl: media.url });
        }

        return beverageEl('div', { className: 'wp-block-beverage-cpt-beverage-block beverage-editor-wrapper' },
            [
                beverageEl('div', { className: 'transplants-beverages-container' },
                    beverageEl('h1', { className: 'transplants-beverages-header' }, 'Transplants Beverages')
                ),
                beverageEl('div', { className: 'content-container' },
                    [
                        beverageEl('p', {}, 'Add a new beverage'),
                        beverageEl(BeverageTextControl, {
                            label: "Beverage Title",
                            value: title,
                            onChange: function(newTitle) {
                                setAttributes({ title: newTitle });
                            }
                        }),
                        beverageEl(BeverageTextControl, {
                            label: "Beverage Description",
                            value: description,
                            onChange: function(newDescription) {
                                setAttributes({ description: newDescription });
                            }
                        }),
                        beverageEl(BeverageTextControl, {
                            label: "Beverage Type",
                            value: type,
                            onChange: function(newType) {
                                setAttributes({ type: newType });
                            }
                        }),
                        beverageEl(BeverageTextControl, {
                            label: "Alcohol Content",
                            value: alcoholContent,
                            onChange: function(newContent) {
                                setAttributes({ alcoholContent: newContent });
                            }
                        }),
                        beverageEl(BeverageToggleControl, {
                            label: "Availability",
                            checked: availability,
                            onChange: function(newValue) {
                                setAttributes({ availability: newValue });
                            }
                        }),
                        beverageEl(BeverageTextControl, { // Change to TextControl for integer input
                            label: "Position",
                            type: 'number', // Set the input type to number
                            value: position || 1, // Set default value to 1 (integer)
                            onChange: function(newPosition) {
                                setAttributes({ position: newPosition });
                            }
                        }),
                         
                        beverageEl('div', { className: 'beverage-form-container' },
                            beverageEl(BeverageMediaUploadCheck, null,
                                beverageEl(BeverageMediaUpload, {
                                    onSelect: onSelectImage,
                                    allowedTypes: ['image'],
                                    render: function(obj) {
                                        return imageUrl ? beverageEl('img', { src: imageUrl, alt: "Beverage Image", onClick: obj.open }) :
                                            beverageEl(BeveragePlaceholder, {
                                                icon: "format-image",
                                                label: "Upload Image",
                                                instructions: "Upload the beverage's image here"
                                            },
                                            beverageEl(BeverageButton, { onClick: obj.open, isPrimary: true }, 'Upload Image')
                                            );
                                    }
                                })
                            )
                        )
                    ]
                )
            ]
        );
    },
    
    save: function() {
        return null;
    }
});
