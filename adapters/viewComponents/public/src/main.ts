import { bootstrapApplication } from '@angular/platform-browser';
import { Component } from '@angular/core';

@Component({
    selector: 'app-root',
    standalone: true,
    template: `
    <h1>Angular funcionando</h1>
    <p>El frontend está activo.</p>
  `
})
class AppComponent { }

bootstrapApplication(AppComponent)
    .catch(err => console.error(err));