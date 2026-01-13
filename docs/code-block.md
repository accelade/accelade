# Code Block Component

The Code Block component provides a beautiful, feature-rich way to display code snippets in your application. It includes syntax highlighting, a macOS-style title bar, copy functionality, and the ability to export code as an image.

## Basic Usage

Use the `x-accelade::code-block` component to display code with syntax highlighting:

```blade
<x-accelade::code-block language="php">
function greet(string $name): string
{
    return "Hello, {$name}!";
}
</x-accelade::code-block>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `language` | string | `'markup'` | The programming language for syntax highlighting |
| `filename` | string | `null` | Optional filename to display in the title bar |

## Supported Languages

The component uses Prism.js for syntax highlighting and supports all major languages:

- `php` - PHP code
- `blade` - Laravel Blade templates (highlighted as PHP)
- `javascript` / `js` - JavaScript
- `typescript` / `ts` - TypeScript
- `html` - HTML markup
- `css` - CSS styles
- `bash` / `shell` - Command line
- `json` - JSON data
- `sql` - SQL queries
- `yaml` - YAML configuration
- `vue` - Vue.js components
- And many more...

## With Filename

Display a custom filename in the title bar:

```blade
<x-accelade::code-block language="php" filename="app/Models/User.php">
class User extends Model
{
    protected $fillable = ['name', 'email'];
}
</x-accelade::code-block>
```

## Features

### Copy to Clipboard

Every code block includes a "Copy" button that copies the code content to the clipboard. When clicked, it shows a "Copied!" confirmation.

### Export as Image

The "Image" button allows users to download the code block as a PNG image. This is perfect for:

- Sharing code snippets on social media
- Creating documentation screenshots
- Embedding in presentations

### macOS-Style Title Bar

The component features a sleek title bar with:

- Traffic light buttons (decorative)
- Language or filename label
- Action buttons (Copy, Image)

## Styling

The code block uses a dark theme by default with:

- Slate background (`#1e293b`)
- Rounded corners
- Subtle border and shadow
- Responsive horizontal scrolling for long lines

## Examples

### PHP Example

```blade
<x-accelade::code-block language="php">
&lt;?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'title' => 'Welcome'
        ]);
    }
}
</x-accelade::code-block>
```

### JavaScript Example

```blade
<x-accelade::code-block language="javascript" filename="app.js">
const greeting = (name) => {
    console.log(`Hello, ${name}!`);
};

greeting('World');
</x-accelade::code-block>
```

### Blade Template Example

```blade
<x-accelade::code-block language="blade" filename="welcome.blade.php">
&lt;div class="container"&gt;
    &lt;h1&gt;{{ $title }}&lt;/h1&gt;
    @foreach($items as $item)
        &lt;p&gt;{{ $item-&gt;name }}&lt;/p&gt;
    @endforeach
&lt;/div&gt;
</x-accelade::code-block>
```

## Tips

1. **Escape HTML entities** - When displaying HTML or Blade code, use `&lt;` and `&gt;` for angle brackets
2. **Preserve whitespace** - The component preserves all whitespace and indentation
3. **Use meaningful filenames** - When using the `filename` prop, use realistic file paths for context
