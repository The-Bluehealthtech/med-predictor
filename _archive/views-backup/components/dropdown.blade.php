@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative dropdown-container" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open" class="dropdown-trigger">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }} dropdown-content"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>

<script>
// Fallback JavaScript for dropdowns if Alpine.js is not working
document.addEventListener('DOMContentLoaded', function() {
    // Check if Alpine.js is working
    setTimeout(function() {
        const dropdowns = document.querySelectorAll('.dropdown-container');
        dropdowns.forEach(function(dropdown) {
            const trigger = dropdown.querySelector('.dropdown-trigger');
            const content = dropdown.querySelector('.dropdown-content');
            
            if (trigger && content) {
                // Add click handler
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isVisible = content.style.display !== 'none';
                    
                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-content').forEach(function(otherContent) {
                        if (otherContent !== content) {
                            otherContent.style.display = 'none';
                            otherContent.style.opacity = '0';
                            otherContent.style.transform = 'scale(0.95)';
                        }
                    });
                    
                    // Toggle current dropdown
                    if (isVisible) {
                        content.style.display = 'none';
                        content.style.opacity = '0';
                        content.style.transform = 'scale(0.95)';
                    } else {
                        content.style.display = 'block';
                        content.style.opacity = '1';
                        content.style.transform = 'scale(1)';
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        content.style.display = 'none';
                        content.style.opacity = '0';
                        content.style.transform = 'scale(0.95)';
                    }
                });
            }
        });
    }, 100); // Small delay to ensure Alpine.js has time to initialize
});
</script> 