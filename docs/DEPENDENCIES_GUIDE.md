# Dependencies Quick Reference Guide

This guide provides quick reference information for all installed dependencies in the Global Harmony Initiative project.

---

## PHP Dependencies (Composer)

### Database & ORM
- **doctrine/dbal** - Database abstraction layer
  ```php
  use Doctrine\DBAL\DriverManager;
  $connection = DriverManager::getConnection($params);
  ```

### Validation
- **symfony/validator** - Validation component
  ```php
  use Symfony\Component\Validator\Validation;
  $validator = Validation::createValidator();
  $violations = $validator->validate($value, $constraints);
  ```

### Authentication
- **delight-im/auth** - Authentication library
  ```php
  use Delight\Auth\Auth;
  $auth = new Auth($db);
  $auth->login('email@example.com', 'password');
  ```

### Email
- **symfony/mailer** - Email sending
  ```php
  use Symfony\Component\Mailer\Mailer;
  use Symfony\Component\Mime\Email;
  $email = (new Email())->from('from@example.com')->to('to@example.com');
  $mailer->send($email);
  ```

### File System
- **symfony/filesystem** - Filesystem utilities
  ```php
  use Symfony\Component\Filesystem\Filesystem;
  $filesystem = new Filesystem();
  $filesystem->mkdir('/path/to/directory');
  ```

- **league/flysystem** - Filesystem abstraction
  ```php
  use League\Flysystem\Filesystem;
  use League\Flysystem\Local\LocalFilesystemAdapter;
  $adapter = new LocalFilesystemAdapter('/path/to/root');
  $filesystem = new Filesystem($adapter);
  ```

### Security
- **symfony/security-csrf** - CSRF protection
  ```php
  use Symfony\Component\Security\Csrf\CsrfTokenManager;
  $tokenManager = new CsrfTokenManager();
  $token = $tokenManager->getToken('form_intent');
  ```

### Logging
- **monolog/monolog** - Logging library
  ```php
  use Monolog\Logger;
  use Monolog\Handler\StreamHandler;
  $logger = new Logger('name');
  $logger->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));
  $logger->warning('Foo');
  ```

### Environment
- **symfony/dotenv** - Environment variable management
  ```php
  use Symfony\Component\Dotenv\Dotenv;
  $dotenv = new Dotenv();
  $dotenv->load(__DIR__.'/.env');
  $dbHost = $_ENV['DB_HOST'];
  ```

### HTTP Client
- **guzzlehttp/guzzle** - HTTP client
  ```php
  use GuzzleHttp\Client;
  $client = new Client();
  $response = $client->request('GET', 'https://api.example.com');
  ```

### Caching
- **symfony/cache** - Caching component
  ```php
  use Symfony\Component\Cache\Adapter\FilesystemAdapter;
  $cache = new FilesystemAdapter();
  $item = $cache->getItem('cache_key');
  $item->set('value');
  $cache->save($item);
  ```

### Templates
- **twig/twig** - Template engine
  ```php
  use Twig\Environment;
  use Twig\Loader\FilesystemLoader;
  $loader = new FilesystemLoader('/path/to/templates');
  $twig = new Environment($loader);
  echo $twig->render('index.html', ['name' => 'World']);
  ```

### Events
- **symfony/event-dispatcher** - Event system
  ```php
  use Symfony\Component\EventDispatcher\EventDispatcher;
  $dispatcher = new EventDispatcher();
  $dispatcher->dispatch(new CustomEvent(), CustomEvent::NAME);
  ```

---

## JavaScript Dependencies (NPM)

### HTTP Client
- **axios** - HTTP client
  ```javascript
  import axios from 'axios';
  const response = await axios.get('/api/endpoint');
  ```

### Validation
- **zod** - Schema validation
  ```javascript
  import { z } from 'zod';
  const schema = z.object({ name: z.string(), age: z.number() });
  const data = schema.parse({ name: 'John', age: 30 });
  ```

### Date Manipulation
- **dayjs** - Date manipulation library
  ```javascript
  import dayjs from 'dayjs';
  dayjs().format('YYYY-MM-DD');
  dayjs().add(1, 'day');
  ```

### Utilities
- **lodash-es** - Utility functions
  ```javascript
  import { debounce, throttle, cloneDeep } from 'lodash-es';
  const debouncedFn = debounce(fn, 300);
  ```

### Notifications
- **notyf** - Toast notifications
  ```javascript
  import { Notyf } from 'notyf';
  const notyf = new Notyf();
  notyf.success('Success message');
  notyf.error('Error message');
  ```

### Forms
- **form-serialize** - Form serialization
  ```javascript
  import serialize from 'form-serialize';
  const data = serialize(formElement, { hash: true });
  ```

### Modals
- **micromodal** - Modal dialogs
  ```javascript
  import MicroModal from 'micromodal';
  MicroModal.init();
  MicroModal.show('modal-id');
  ```

### File Upload
- **filepond** - File upload component
  ```javascript
  import { FilePond, registerPlugin } from 'filepond';
  FilePond.create(inputElement);
  ```

### Data Tables
- **tabulator-tables** - Data tables
  ```javascript
  import { Tabulator } from 'tabulator-tables';
  const table = new Tabulator('#example-table', { data: dataArray });
  ```

### Rich Text Editor
- **quill** - Rich text editor
  ```javascript
  import Quill from 'quill';
  const quill = new Quill('#editor', { theme: 'snow' });
  ```

### Charts
- **chart.js** - Charting library
  ```javascript
  import Chart from 'chart.js/auto';
  new Chart(ctx, { type: 'bar', data: data });
  ```

### State Management
- **zustand** - State management
  ```javascript
  import create from 'zustand';
  const useStore = create(set => ({ count: 0, increment: () => set(state => ({ count: state.count + 1 })) }));
  ```

### Error Tracking
- **@sentry/browser** - Error tracking
  ```javascript
  import * as Sentry from '@sentry/browser';
  Sentry.init({ dsn: 'your-dsn' });
  Sentry.captureException(error);
  ```

### Animations
- **gsap** - Animation library
  ```javascript
  import { gsap } from 'gsap';
  gsap.to('.element', { x: 100, duration: 1 });
  ```

### Testing
- **vitest** - Testing framework
  ```javascript
  import { describe, it, expect } from 'vitest';
  describe('My Test', () => { it('should work', () => { expect(true).toBe(true); }); });
  ```

---

## Development Tools

### PHP Development Tools
- **rector/rector** - Automated refactoring
- **friendsofphp/php-cs-fixer** - Code style fixer
- **phpstan/phpstan** - Static analysis
- **phpmd/phpmd** - Code smell detection
- **symfony/var-dumper** - Enhanced debugging
- **robmorgan/phinx** - Database migrations

### JavaScript Development Tools
- **prettier** - Code formatter
- **eslint** - JavaScript linter
- **eslint-config-prettier** - ESLint/Prettier integration
- **jscpd** - Code duplication detector
- **vite** - Modern build tool
- **typescript** - TypeScript compiler (optional)

---

## Quick Start Examples

### PHP: Using Environment Variables
```php
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');
```

### PHP: Sending Email
```php
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
$email = (new Email())
    ->from('noreply@example.com')
    ->to('user@example.com')
    ->subject('Hello')
    ->text('Hello World!');
$mailer->send($email);
```

### JavaScript: Making API Calls
```javascript
import axios from 'axios';
const response = await axios.post('/api/endpoint', { data: 'value' });
```

### JavaScript: Form Validation
```javascript
import { z } from 'zod';
const schema = z.object({
  email: z.string().email(),
  password: z.string().min(8)
});
try {
  const data = schema.parse(formData);
} catch (error) {
  console.error(error.errors);
}
```

---

## Package Choices Made

### PHP Alternatives Selected:
- **doctrine/dbal** over illuminate/database (lighter weight)
- **symfony/validator** over respect/validation (Symfony ecosystem consistency)
- **delight-im/auth** over phpauth/phpauth (more modern)
- **symfony/mailer** over phpmailer/phpmailer (Symfony ecosystem consistency)
- **symfony/security-csrf** over slim/csrf (Symfony ecosystem consistency)
- **twig/twig** over league/plates (more popular and feature-rich)

### JavaScript Alternatives Selected:
- **zod** over yup (more modern, TypeScript-friendly)
- **dayjs** over date-fns (smaller bundle size)
- **filepond** over uppy (more modern, better Vue/React integration)
- **tabulator-tables** over datatables.net-dt (more modern, better performance)
- **quill** over tinymce (lighter weight)
- **chart.js** over apexcharts (more popular, easier to use)

---

## Installation Status

✅ All dependencies have been successfully installed and are ready to use.

For detailed usage instructions, refer to each package's official documentation.

