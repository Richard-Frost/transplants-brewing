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
    ToggleControl: EventToggleControl
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
            default: '[]',
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
        event_date: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_date'
        },
        event_time: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_time'
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
        },
        event_type: {
            type: 'string',
            default: '',
            source: 'meta',
            meta: 'event_type'
        },
        featured_event: {
            type: 'boolean',
            default: false,
            source: 'meta',
            meta: 'featured_event'
        }
    },
    edit: function(props) {
        const {
            attributes: {
                event_title, performers, event_description, event_image_url,
                event_date, event_time, event_price, event_link, event_age, 
                event_type, featured_event
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

        function onEventTypeChange(event) {
            setAttributes({ event_type: event.target.value });
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
                            onChange: newTitle => setAttributes({ event_title: newTitle })
                        }),
                        eventEl(EventTextControl, {
                            label: "Event Description",
                            value: event_description,
                            onChange: newDescription => setAttributes({ event_description: newDescription })
                        }),
                        eventEl(EventTextControl, {
                            label: "Event Date",
                            value: event_date,
                            onChange: newDate => setAttributes({ event_date: newDate }),
                            type: 'date'
                        }),
                        eventEl(EventTextControl, {
                            label: "Event Time",
                            value: event_time,
                            onChange: newTime => setAttributes({ event_time: newTime })
                        }),
                        eventEl(EventTextControl, {
                            label: "Price",
                            value: event_price,
                            onChange: newPrice => setAttributes({ event_price: newPrice })
                        }),
                        eventEl(EventTextControl, {
                            label: "Link",
                            value: event_link,
                            onChange: newLink => setAttributes({ event_link: newLink })
                        }),
                        eventEl(EventTextControl, {
                            label: "Age",
                            value: event_age,
                            onChange: newAge => setAttributes({ event_age: newAge })
                        }),
                        eventEl('select', {
                            onChange: onEventTypeChange,
                            value: event_type,
                            style: { width: '100%', height: '30px', marginBottom: '20px' }
                        },
                            eventEl('option', { value: '' }, 'Select Event Type'),
                            eventEl('option', { value: 'concert' }, 'CONCERT'),
                            eventEl('option', { value: 'dj' }, 'DJ'),
                            eventEl('option', { value: 'comedy' }, 'COMEDY'),
                            eventEl('option', { value: 'burlesque' }, 'BURLESQUE'),
                            eventEl('option', { value: 'wrestling' }, 'WRESTLING')
                        ),
                        eventEl(EventToggleControl, {
                            label: "Featured Event",
                            checked: featured_event,
                            onChange: newValue => setAttributes({ featured_event: newValue })
                        }),
                        eventEl('div', { className: 'performers-section' },
                            performersArray.map(performer =>
                                eventEl('div', { key: performer.id, className: 'performer-entry' },
                                    [
                                        eventEl(EventTextControl, {
                                            label: `Performer ${performer.hierarchy}`,
                                            value: performer.name,
                                            onChange: newName => updatePerformerName(performer.id, newName)
                                        }),
                                        eventEl(EventButton, {
                                            isDestructive: true,
                                            onClick: () => removePerformer(performer.id)
                                        }, 'Remove')
                                    ]
                                )
                            ),
                            eventEl(EventButton, { isSecondary: true, onClick: addPerformer }, '+ Add Performer')
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
                                            render: ({ open }) => eventEl(EventButton, { onClick: open, isPrimary: true }, 'Upload Image')
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
