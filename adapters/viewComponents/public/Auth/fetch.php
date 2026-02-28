<script>
    if (typeof window.App === "undefined" || typeof window.App !== "function") {
        window.App = class App {};
    }
    if (typeof window.app === "undefined" || !(window.app instanceof window.App)) {
        window.app = new window.App();
    }
    window.App.prototype.fetch = function(adapter, endpoint, body) {
        //entry errors
        if (!adapter || !endpoint) {
            return Promise.reject(new Error("Both 'adapter' and 'endpoint' parameters are required."));
        }

        //build fetch
        let baseUrl = false;
        switch (adapter) {
            case 'userManager':
                baseUrl = '<?= getenv('USER_MANAGER');?>';
                break;
            case 'controlRoom':
                baseUrl = '<?= getenv('CONTROL_ROOM');?>';
                break;
            case 'viewComponents':
                baseUrl = '<?= getenv('VIEW_COMPONENTS');?>';
                break;

            default:
                break;
        }

        if (!baseUrl) {
            return Promise.reject(new Error("Unknown adapter or baseUrl not set"));
        }

        const url = baseUrl + '/' + endpoint;
        const options = {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "include", // Enables cookies/session for cross-origin fetch
            mode: "cors" // Explicitly allow cross-origin fetch
        };

        if (body) {
            options.body = JSON.stringify(body);
        }

        return fetch(url, options)
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    throw new Error(`Fetch failed: ${res.status} ${res.statusText} - ${text}`);
                });
            }
            // To decode as JSON
            return res.json();
        });

    };

    window.App.prototype.handleFormSubmit = function(e) {
        e.preventDefault();
        const form = e.target;
        const method = (form.getAttribute('method') || 'post').toUpperCase(); // Get adapter and endpoint values from hidden inputs
        const adapter = form.querySelector('input[name="adapter"]')?.value;
        const endpoint = form.querySelector('input[name="endpoint"]')?.value;

        if (!adapter || !endpoint) {
            alert("Error");
            return;
        }

        // Serialize form data to an object
        const formData = new FormData(form);
        let body = {};
        formData.forEach((value, key) => {
            body[key] = value;
        });

        // call the fetch function defined above
        app.fetch(adapter, endpoint, body)
            .then(response => {
                // Handle success, e.g. show message or redirect
                if (response && response.redirect) {
                    window.location.href = response.redirect;
                } else if (response && response.message) {
                    alert(response.message);
                } else {
                    alert("Success");
                }
            })
            .catch(err => {
                alert("Error: " + err.message);
            });
    };
    //EVENT
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList && e.target.classList.contains('fetchform')) {
            app.handleFormSubmit(e);
        }
    });


</script>