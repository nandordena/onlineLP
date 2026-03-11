<script>
<?=initJsClass("App","app")?>
window.App.prototype.eventHandeler = function(e) {
    let target = e.target;
    let eventType = e.type;
    while (target && target !== window.document) {
        const eventRef = target.getAttribute && target.getAttribute('data-event-'+eventType);
        if (eventRef) {
            // Support dot notation, e.g. "myNamespace.myFunc"
            const handler = eventRef.split('.').reduce((obj, key) => obj && obj[key], window);
            if (typeof handler === 'function') {
                const eventData = target.getAttribute && target.getAttribute('data-event-data');
                handler(e, eventData);
                return;
            }
        }
        target = target.parentNode;
    }
}
let eventListeners = [
    'click'
    ,'change'
];
eventListeners.forEach(eventType => {
    document.addEventListener(eventType, app.eventHandeler);
});

</script>