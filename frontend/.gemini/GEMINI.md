
You are an expert in TypeScript, Angular, and scalable web application development. You write functional, maintainable, performant, and accessible code following Angular and TypeScript best practices.

## TypeScript Best Practices

- Use strict type checking
- Prefer type inference when the type is obvious
- Avoid the `any` type; use `unknown` when type is uncertain

## Angular Best Practices

- Always use standalone components over NgModules
- Must NOT set `standalone: true` inside Angular decorators. It's the default in Angular v20+.
- Use signals for state management
- Implement lazy loading for feature routes
- Do NOT use the `@HostBinding` and `@HostListener` decorators. Put host bindings inside the `host` object of the `@Component` or `@Directive` decorator instead
- Use `NgOptimizedImage` for all static images.
  - `NgOptimizedImage` does not work for inline base64 images.

## Accessibility Requirements

- It MUST pass all AXE checks.
- It MUST follow all WCAG AA minimums, including focus management, color contrast, and ARIA attributes.

### Components

- Keep components small and focused on a single responsibility
- Use `input()` and `output()` functions instead of decorators
- Use `computed()` for derived state
- Set `changeDetection: ChangeDetectionStrategy.OnPush` in `@Component` decorator
- Prefer inline templates for small components
- Prefer Reactive forms instead of Template-driven ones
- Do NOT use `ngClass`, use `class` bindings instead
- Do NOT use `ngStyle`, use `style` bindings instead
- When using external templates/styles, use paths relative to the component TS file.

## State Management

- Use signals for local component state
- Use `computed()` for derived state
- Keep state transformations pure and predictable
- Do NOT use `mutate` on signals, use `update` or `set` instead

## Templates

- Keep templates simple and avoid complex logic
- Use native control flow (`@if`, `@for`, `@switch`) instead of `*ngIf`, `*ngFor`, `*ngSwitch`
- Use the async pipe to handle observables
- Do not assume globals like (`new Date()`) are available.
- Do not write arrow functions in templates (they are not supported).

## Services

- Design services around a single responsibility
- Use the `providedIn: 'root'` option for singleton services
- Use the `inject()` function instead of constructor injection

## Styling & CSS (BEM Methodology)

- STRICT BEM: Use Block-Element-Modifier for all styles. No utility-first frameworks (like Tailwind).
- SCSS Structure: Use Sass with the 7-1 pattern.
- Encapsulation: Prefer ViewEncapsulation. Emulated (default) to keep BEM classes scoped to the component.
- Nesting: Limit SCSS nesting to a maximum of 3 levels to maintain readability.
- Variables: Always use CSS variables or SCSS tokens for colors, spacing, and status (e.g., $color-success for stock status).

# Architecture (Domain-Driven)

- Folder Structure: Organize by core/ (singletons), shared/ (reusable components/pipes), and features/ (domain-specific logic like inventory, auth, orders).
- Data Access: Use the inject(HttpClient) pattern inside Services. Services must return typed Observables using Interfaces defined in core/models.
- Interceptors: All API calls must pass through authInterceptor (for credentials) and errorInterceptor (for global error handling).

# API & Offline-First

- Mocking: Use a MockInterceptor during development to simulate API responses before Symfony endpoints are ready.
- Offline Storage: Use Dexie.js for local persistence. Services should implement a "Stale-While-Revalidate" pattern when necessary.
- Error Handling: Use a global LoggerService that can be disabled in production.

# Security

- JWT & Cookies: Use withCredentials: true in all HTTP requests to support HttpOnly Cookies.
- Auth State: Use a Signal in AuthService to track isAuthenticated.
