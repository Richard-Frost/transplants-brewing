const eventRegisterBlockType = wp.blocks.registerBlockType;

const {
    RichText: EventRichText,
    MediaUpload: EventMediaUpload,
    MediaUploadCheck: EventMediaUploadCheck
} = wp.blockEditor || wp.editor;

const {
    TextControl: EventTextControl,
    Button: EventButton,
    Placeholder: EventPlaceholder,
    DateTimePicker: EventDateTimePicker
} = wp.components;

const eventEl = wp.element.createElement;

eventRegisterBlockType('event-cpt/event-block', {
    title: 'Event Block',
    icon: 'megaphone',
    category: 'common',
    attributes: {
        event_title: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_title'
        },
        performers: {
            type: 'string',
            default: '[]',  // Use a JSON representation of an empty array
            source: 'meta',
            meta: 'performers'
        },
        event_description: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_description'
        },
        event_image_url: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_image_url'
        },
        event_date_time: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_date_time'
        },
        event_price: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_price'
        },
        event_link: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_link'
        },
        event_age: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_age'
        }
    },
    edit: function(props) {
        const { 
            attributes: { 
                event_title, event_description, event_image_url, 
                event_date_time, event_price, event_link, event_age,
                performers
            }, 
            setAttributes 
        } = props;

        const performersArray = JSON.parse(performers || '[]');

        function onSelectImage(media) {
            setAttributes({ event_image_url: media.url });
        }

        function addPerformer() {
            const currentPerformers = [...performersArray];
            const newPerformer = {
                id: Date.now(),
                name: '',
                hierarchy: currentPerformers.length + 1
            };
            currentPerformers.push(newPerformer);
            setAttributes({ performers: JSON.stringify(currentPerformers) });
        }

        function updatePerformerName(id, newName) {
            const updatedPerformers = performersArray.map(performer => {
                if (performer.id === id) {
                    return { ...performer, name: newName };
                }
                return performer;
            });
            setAttributes({ performers: JSON.stringify(updatedPerformers) });
        }

        function removePerformer(id) {
            const updatedPerformers = performersArray.filter(performer => performer.id !== id);
            setAttributes({ performers: JSON.stringify(updatedPerformers) });
        }

        return eventEl('div', { className: 'wp-block-event-cpt-event-block event-editor-wrapper' },
            [
                eventEl('div', { className: 'transplants-events-container' }, 
                    eventEl('h1', { className: 'transplants-events-header' }, 'Transplants Events')
                ),
                eventEl('div', { className: 'content-container' }, 
                    [
                        eventEl('p', {}, 'Add a new event'),
                        eventEl(EventTextControl, {
                            label: "Event Title",
                            value: event_title,
                            onChange: function(newTitle) {
                                setAttributes({ event_title: newTitle });
                            }
                        }),
                        eventEl(EventTextControl, {
                            label: "Event Description",
                            value: event_description,
                            onChange: function(newDescription) {
                                setAttributes({ event_description: newDescription });
                            }
                        }),
                        eventEl(EventDateTimePicker, {
                            currentDate: event_date_time,
                            onChange: (newDate) => setAttributes({ event_date_time: newDate })
                        }),
                        eventEl(EventTextControl, {
                            label: "Price",
                            value: event_price,
                            onChange: (newPrice) => setAttributes({ event_price: newPrice })
                        }),
                        eventEl(EventTextControl, {
                            label: "Link",
                            value: event_link,
                            onChange: (newLink) => setAttributes({ event_link: newLink })
                        }),
                        eventEl(EventTextControl, {
                            label: "Age",
                            value: event_age,
                            onChange: (newAge) => setAttributes({ event_age: newAge })
                        }),
                        eventEl('div', { className: 'performers-section' },
                            [
                                eventEl('h2', {}, 'Performers'),
                                performersArray && performersArray.length > 0 ? 
                                    performersArray.map(performer => 
                                        eventEl('div', { key: performer.id, className: 'performer-entry' },
                                            [
                                                eventEl(EventTextControl, {
                                                    label: `Performer ${performer.hierarchy}`,
                                                    value: performer.name,
                                                    onChange: (newName) => updatePerformerName(performer.id, newName)
                                                }),
                                                eventEl(EventButton, {
                                                    isDestructive: true,
                                                    onClick: () => removePerformer(performer.id)
                                                }, 'Remove')
                                            ]
                                        )
                                    ) : null,
                                eventEl(EventButton, { isSecondary: true, onClick: addPerformer }, '+ Add Performer')
                            ]
                        ),
                        eventEl('div', { className: 'image-section' },
                            event_image_url ? 
                                eventEl('img', { src: event_image_url, alt: 'Event Image', className: 'uploaded-event-image' }) :
                                eventEl(EventPlaceholder, {
                                    icon: 'format-image',
                                    label: 'Upload Event Image'
                                }, 
                                    eventEl(EventMediaUploadCheck, {}, 
                                        eventEl(EventMediaUpload, {
                                            onSelect: onSelectImage,
                                            allowedTypes: ['image'],
                                            value: event_image_url,
                                            render: function({ open }) {
                                                return eventEl(EventButton, {
                                                    onClick: open,
                                                    isPrimary: true
                                                }, 'Upload Image');
                                            }
                                        })
                                    )
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
