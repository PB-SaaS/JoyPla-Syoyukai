/*
! tailwindcss v3.2.1 | MIT License | https://tailwindcss.com
*/

/*
1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
*/

*,
::before,
::after {
box-sizing: border-box;
/* 1 */
border-width: 0;
/* 2 */
border-style: solid;
/* 2 */
border-color: #e5e7eb;
/* 2 */
}

::before,
::after {
--tw-content: '';
}

/*
1. Use a consistent sensible line-height in all browsers.
2. Prevent adjustments of font size after orientation changes in iOS.
3. Use a more readable tab size.
4. Use the user's configured `sans` font-family by default.
*/

html {
line-height: 1.5;
/* 1 */
-webkit-text-size-adjust: 100%;
/* 2 */
-moz-tab-size: 4;
/* 3 */
-o-tab-size: 4;
tab-size: 4;
/* 3 */
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
/* 4 */
}

/*
1. Remove the margin in all browsers.
2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
*/

body {
margin: 0;
/* 1 */
line-height: inherit;
/* 2 */
}

/*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/

hr {
height: 0;
/* 1 */
color: inherit;
/* 2 */
border-top-width: 1px;
/* 3 */
}

/*
Add the correct text decoration in Chrome, Edge, and Safari.
*/

abbr:where([title]) {
-webkit-text-decoration: underline dotted;
text-decoration: underline dotted;
}

/*
Remove the default font size and weight for headings.
*/

h1,
h2,
h3,
h4,
h5,
h6 {
font-size: inherit;
font-weight: inherit;
}

/*
Reset links to optimize for opt-in styling instead of opt-out.
*/

a {
color: inherit;
text-decoration: inherit;
}

/*
Add the correct font weight in Edge and Safari.
*/

b,
strong {
font-weight: bolder;
}

/*
1. Use the user's configured `mono` font family by default.
2. Correct the odd `em` font sizing in all browsers.
*/

code,
kbd,
samp,
pre {
font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
/* 1 */
font-size: 1em;
/* 2 */
}

/*
Add the correct font size in all browsers.
*/

small {
font-size: 80%;
}

/*
Prevent `sub` and `sup` elements from affecting the line height in all browsers.
*/

sub,
sup {
font-size: 75%;
line-height: 0;
position: relative;
vertical-align: baseline;
}

sub {
bottom: -0.25em;
}

sup {
top: -0.5em;
}

/*
1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
3. Remove gaps between table borders by default.
*/

table {
text-indent: 0;
/* 1 */
border-color: inherit;
/* 2 */
border-collapse: collapse;
/* 3 */
}

/*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/

button,
input,
optgroup,
select,
textarea {
font-family: inherit;
/* 1 */
font-size: 100%;
/* 1 */
font-weight: inherit;
/* 1 */
line-height: inherit;
/* 1 */
color: inherit;
/* 1 */
margin: 0;
/* 2 */
padding: 0;
/* 3 */
}

/*
Remove the inheritance of text transform in Edge and Firefox.
*/

button,
select {
text-transform: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/

button,
[type='button'],
[type='reset'],
[type='submit'] {
-webkit-appearance: button;
/* 1 */
background-color: transparent;
/* 2 */
background-image: none;
/* 2 */
}

/*
Use the modern Firefox focus style for all focusable elements.
*/

:-moz-focusring {
outline: auto;
}

/*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/

:-moz-ui-invalid {
box-shadow: none;
}

/*
Add the correct vertical alignment in Chrome and Firefox.
*/

progress {
vertical-align: baseline;
}

/*
Correct the cursor style of increment and decrement buttons in Safari.
*/

::-webkit-inner-spin-button,
::-webkit-outer-spin-button {
height: auto;
}

/*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/

[type='search'] {
-webkit-appearance: textfield;
/* 1 */
outline-offset: -2px;
/* 2 */
}

/*
Remove the inner padding in Chrome and Safari on macOS.
*/

::-webkit-search-decoration {
-webkit-appearance: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/

::-webkit-file-upload-button {
-webkit-appearance: button;
/* 1 */
font: inherit;
/* 2 */
}

/*
Add the correct display in Chrome and Safari.
*/

summary {
display: list-item;
}

/*
Removes the default spacing and border for appropriate elements.
*/

blockquote,
dl,
dd,
h1,
h2,
h3,
h4,
h5,
h6,
hr,
figure,
p,
pre {
margin: 0;
}

fieldset {
margin: 0;
padding: 0;
}

legend {
padding: 0;
}

ol,
ul,
menu {
list-style: none;
margin: 0;
padding: 0;
}

/*
Prevent resizing textareas horizontally by default.
*/

textarea {
resize: vertical;
}

/*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/

input::-moz-placeholder, textarea::-moz-placeholder {
opacity: 1;
/* 1 */
color: #9ca3af;
/* 2 */
}

input::placeholder,
textarea::placeholder {
opacity: 1;
/* 1 */
color: #9ca3af;
/* 2 */
}

/*
Set the default cursor for buttons.
*/

button,
[role="button"] {
cursor: pointer;
}

/*
Make sure disabled buttons don't get the pointer cursor.
*/

:disabled {
cursor: default;
}

/*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
This can trigger a poorly considered lint error in some tools but is included by design.
*/

img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
display: block;
/* 1 */
vertical-align: middle;
/* 2 */
}

/*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/

img,
video {
max-width: 100%;
height: auto;
}

/* Make elements with the HTML hidden attribute stay hidden by default */

[hidden] {
display: none;
}

*, ::before, ::after {
--tw-border-spacing-x: 0;
--tw-border-spacing-y: 0;
--tw-translate-x: 0;
--tw-translate-y: 0;
--tw-rotate: 0;
--tw-skew-x: 0;
--tw-skew-y: 0;
--tw-scale-x: 1;
--tw-scale-y: 1;
--tw-pan-x: ;
--tw-pan-y: ;
--tw-pinch-zoom: ;
--tw-scroll-snap-strictness: proximity;
--tw-ordinal: ;
--tw-slashed-zero: ;
--tw-numeric-figure: ;
--tw-numeric-spacing: ;
--tw-numeric-fraction: ;
--tw-ring-inset: ;
--tw-ring-offset-width: 0px;
--tw-ring-offset-color: #fff;
--tw-ring-color: rgb(59 130 246 / 0.5);
--tw-ring-offset-shadow: 0 0 #0000;
--tw-ring-shadow: 0 0 #0000;
--tw-shadow: 0 0 #0000;
--tw-shadow-colored: 0 0 #0000;
--tw-blur: ;
--tw-brightness: ;
--tw-contrast: ;
--tw-grayscale: ;
--tw-hue-rotate: ;
--tw-invert: ;
--tw-saturate: ;
--tw-sepia: ;
--tw-drop-shadow: ;
--tw-backdrop-blur: ;
--tw-backdrop-brightness: ;
--tw-backdrop-contrast: ;
--tw-backdrop-grayscale: ;
--tw-backdrop-hue-rotate: ;
--tw-backdrop-invert: ;
--tw-backdrop-opacity: ;
--tw-backdrop-saturate: ;
--tw-backdrop-sepia: ;
}

::-webkit-backdrop {
--tw-border-spacing-x: 0;
--tw-border-spacing-y: 0;
--tw-translate-x: 0;
--tw-translate-y: 0;
--tw-rotate: 0;
--tw-skew-x: 0;
--tw-skew-y: 0;
--tw-scale-x: 1;
--tw-scale-y: 1;
--tw-pan-x: ;
--tw-pan-y: ;
--tw-pinch-zoom: ;
--tw-scroll-snap-strictness: proximity;
--tw-ordinal: ;
--tw-slashed-zero: ;
--tw-numeric-figure: ;
--tw-numeric-spacing: ;
--tw-numeric-fraction: ;
--tw-ring-inset: ;
--tw-ring-offset-width: 0px;
--tw-ring-offset-color: #fff;
--tw-ring-color: rgb(59 130 246 / 0.5);
--tw-ring-offset-shadow: 0 0 #0000;
--tw-ring-shadow: 0 0 #0000;
--tw-shadow: 0 0 #0000;
--tw-shadow-colored: 0 0 #0000;
--tw-blur: ;
--tw-brightness: ;
--tw-contrast: ;
--tw-grayscale: ;
--tw-hue-rotate: ;
--tw-invert: ;
--tw-saturate: ;
--tw-sepia: ;
--tw-drop-shadow: ;
--tw-backdrop-blur: ;
--tw-backdrop-brightness: ;
--tw-backdrop-contrast: ;
--tw-backdrop-grayscale: ;
--tw-backdrop-hue-rotate: ;
--tw-backdrop-invert: ;
--tw-backdrop-opacity: ;
--tw-backdrop-saturate: ;
--tw-backdrop-sepia: ;
}

::backdrop {
--tw-border-spacing-x: 0;
--tw-border-spacing-y: 0;
--tw-translate-x: 0;
--tw-translate-y: 0;
--tw-rotate: 0;
--tw-skew-x: 0;
--tw-skew-y: 0;
--tw-scale-x: 1;
--tw-scale-y: 1;
--tw-pan-x: ;
--tw-pan-y: ;
--tw-pinch-zoom: ;
--tw-scroll-snap-strictness: proximity;
--tw-ordinal: ;
--tw-slashed-zero: ;
--tw-numeric-figure: ;
--tw-numeric-spacing: ;
--tw-numeric-fraction: ;
--tw-ring-inset: ;
--tw-ring-offset-width: 0px;
--tw-ring-offset-color: #fff;
--tw-ring-color: rgb(59 130 246 / 0.5);
--tw-ring-offset-shadow: 0 0 #0000;
--tw-ring-shadow: 0 0 #0000;
--tw-shadow: 0 0 #0000;
--tw-shadow-colored: 0 0 #0000;
--tw-blur: ;
--tw-brightness: ;
--tw-contrast: ;
--tw-grayscale: ;
--tw-hue-rotate: ;
--tw-invert: ;
--tw-saturate: ;
--tw-sepia: ;
--tw-drop-shadow: ;
--tw-backdrop-blur: ;
--tw-backdrop-brightness: ;
--tw-backdrop-contrast: ;
--tw-backdrop-grayscale: ;
--tw-backdrop-hue-rotate: ;
--tw-backdrop-invert: ;
--tw-backdrop-opacity: ;
--tw-backdrop-saturate: ;
--tw-backdrop-sepia: ;
}

.container {
width: 100%;
}

@media (min-width: 640px) {
.container {
max-width: 640px;
}
}

@media (min-width: 768px) {
.container {
max-width: 768px;
}
}

@media (min-width: 1024px) {
.container {
max-width: 1024px;
}
}

@media (min-width: 1280px) {
.container {
max-width: 1280px;
}
}

@media (min-width: 1536px) {
.container {
max-width: 1536px;
}
}

[type="checkbox"]:checked {
background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23fff' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}

[type="radio"]:checked {
background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23fff' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
}

[type="checkbox"]:focus,
[type="radio"]:focus {
--tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
--tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(0px + var(--tw-ring-offset-width)) var(--tw-ring-color);
box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
--tw-ring-offset-width: 0px;
}

[type="checkbox"],
[type="checkbox"]:checked,
[type="checkbox"]:checked:hover,
[type="checkbox"]:checked:focus,
[type="checkbox"]:indeterminate:hover,
[type="radio"],
[type="radio"]:checked,
[type="radio"]:checked:hover,
[type="radio"]:checked:focus {
--tw-border-opacity: 1;
border-color: rgb(209 213 219 / var(--tw-border-opacity));
}

.sr-only {
position: absolute;
width: 1px;
height: 1px;
padding: 0;
margin: -1px;
overflow: hidden;
clip: rect(0, 0, 0, 0);
white-space: nowrap;
border-width: 0;
}

.pointer-events-none {
pointer-events: none;
}

.visible {
visibility: visible;
}

.collapse {
visibility: collapse;
}

.static {
position: static;
}

.fixed {
position: fixed;
}

.absolute {
position: absolute;
}

.relative {
position: relative;
}

.sticky {
position: sticky;
}

.inset-0 {
top: 0px;
right: 0px;
bottom: 0px;
left: 0px;
}

.inset-y-0 {
top: 0px;
bottom: 0px;
}

.right-0 {
right: 0px;
}

.left-0 {
left: 0px;
}

.top-\[80px\] {
top: 80px;
}

.top-0 {
top: 0px;
}

.top-2 {
top: 0.5rem;
}

.right-2 {
right: 0.5rem;
}

.-top-1 {
top: -0.25rem;
}

.-bottom-1 {
bottom: -0.25rem;
}

.-left-1 {
left: -0.25rem;
}

.-right-1 {
right: -0.25rem;
}

.-top-2 {
top: -0.5rem;
}

.bottom-4 {
bottom: 1rem;
}

.right-6 {
right: 1.5rem;
}

.left-1 {
left: 0.25rem;
}

.top-1 {
top: 0.25rem;
}

.bottom-0 {
bottom: 0px;
}

.left-auto {
left: auto;
}

.z-50 {
z-index: 50;
}

.z-20 {
z-index: 20;
}

.z-10 {
z-index: 10;
}

.order-last {
order: 9999;
}

.col-span-4 {
grid-column: span 4 / span 4;
}

.col-span-2 {
grid-column: span 2 / span 2;
}

.float-left {
float: left;
}

.m-0 {
margin: 0px;
}

.m-8 {
margin: 2rem;
}

.mx-auto {
margin-left: auto;
margin-right: auto;
}

.my-2 {
margin-top: 0.5rem;
margin-bottom: 0.5rem;
}

.my-6 {
margin-top: 1.5rem;
margin-bottom: 1.5rem;
}

.mx-4 {
margin-left: 1rem;
margin-right: 1rem;
}

.my-4 {
margin-top: 1rem;
margin-bottom: 1rem;
}

.mx-0 {
margin-left: 0px;
margin-right: 0px;
}

.my-5 {
margin-top: 1.25rem;
margin-bottom: 1.25rem;
}

.my-auto {
margin-top: auto;
margin-bottom: auto;
}

.mx-8 {
margin-left: 2rem;
margin-right: 2rem;
}

.-mx-6 {
margin-left: -1.5rem;
margin-right: -1.5rem;
}

.my-0 {
margin-top: 0px;
margin-bottom: 0px;
}

.-mx-8 {
margin-left: -2rem;
margin-right: -2rem;
}

.mx-6 {
margin-left: 1.5rem;
margin-right: 1.5rem;
}

.mt-\[15px\] {
margin-top: 15px;
}

.mb-2 {
margin-bottom: 0.5rem;
}

.mb-8 {
margin-bottom: 2rem;
}

.mb-4 {
margin-bottom: 1rem;
}

.mr-2 {
margin-right: 0.5rem;
}

.mb-96 {
margin-bottom: 24rem;
}

.mt-3 {
margin-top: 0.75rem;
}

.mr-4 {
margin-right: 1rem;
}

.mt-4 {
margin-top: 1rem;
}

.mb-3 {
margin-bottom: 0.75rem;
}

.mb-6 {
margin-bottom: 1.5rem;
}

.ml-auto {
margin-left: auto;
}

.mb-1 {
margin-bottom: 0.25rem;
}

.mt-2 {
margin-top: 0.5rem;
}

.mt-1 {
margin-top: 0.25rem;
}

.mb-0 {
margin-bottom: 0px;
}

.-mb-px {
margin-bottom: -1px;
}

.ml-3 {
margin-left: 0.75rem;
}

.ml-1 {
margin-left: 0.25rem;
}

.mt-6 {
margin-top: 1.5rem;
}

.mt-5 {
margin-top: 1.25rem;
}

.ml-0 {
margin-left: 0px;
}

.mb-5 {
margin-bottom: 1.25rem;
}

.mt-0 {
margin-top: 0px;
}

.mr-\[20px\] {
margin-right: 20px;
}

.mr-\[10px\] {
margin-right: 10px;
}

.mb-\[20px\] {
margin-bottom: 20px;
}

.mt-\[20px\] {
margin-top: 20px;
}

.ml-\[-30px\] {
margin-left: -30px;
}

.ml-\[30px\] {
margin-left: 30px;
}

.box-border {
box-sizing: border-box;
}

.block {
display: block;
}

.inline-block {
display: inline-block;
}

.inline {
display: inline;
}

.flex {
display: flex;
}

.inline-flex {
display: inline-flex;
}

.table {
display: table;
}

.flow-root {
display: flow-root;
}

.grid {
display: grid;
}

.contents {
display: contents;
}

.hidden {
display: none;
}

.h-screen {
height: 100vh;
}

.h-full {
height: 100%;
}

.h-72 {
height: 18rem;
}

.h-32 {
height: 8rem;
}

.h-10 {
height: 2.5rem;
}

.h-8 {
height: 2rem;
}

.h-6 {
height: 1.5rem;
}

.h-12 {
height: 3rem;
}

.h-4 {
height: 1rem;
}

.h-11 {
height: 2.75rem;
}

.h-3 {
height: 0.75rem;
}

.h-44 {
height: 11rem;
}

.h-auto {
height: auto;
}

.h-\[30px\] {
height: 30px;
}

.h-\[18px\] {
height: 18px;
}

.max-h-full {
max-height: 100%;
}

.max-h-\[450px\] {
max-height: 450px;
}

.min-h-\[80px\] {
min-height: 80px;
}

.w-screen {
width: 100vw;
}

.w-full {
width: 100%;
}

.w-16 {
width: 4rem;
}

.w-32 {
width: 8rem;
}

.w-44 {
width: 11rem;
}

.w-20 {
width: 5rem;
}

.w-8 {
width: 2rem;
}

.w-6 {
width: 1.5rem;
}

.w-10 {
width: 2.5rem;
}

.w-14 {
width: 3.5rem;
}

.w-4 {
width: 1rem;
}

.w-36 {
width: 9rem;
}

.w-64 {
width: 16rem;
}

.w-28 {
width: 7rem;
}

.w-auto {
width: auto;
}

.w-3 {
width: 0.75rem;
}

.w-48 {
width: 12rem;
}

.w-96 {
width: 24rem;
}

.w-24 {
width: 6rem;
}

.w-1 {
width: 0.25rem;
}

.w-5 {
width: 1.25rem;
}

.w-2 {
width: 0.5rem;
}

.w-1\/3 {
width: 33.333333%;
}

.w-1\/2 {
width: 50%;
}

.w-\[30px\] {
width: 30px;
}

.w-5\/6 {
width: 83.333333%;
}

.w-2\/3 {
width: 66.666667%;
}

.w-1\/4 {
width: 25%;
}

.w-1\/5 {
width: 20%;
}

.w-4\/5 {
width: 80%;
}

.min-w-full {
min-width: 100%;
}

.min-w-\[18px\] {
min-width: 18px;
}

.min-w-\[1px\] {
min-width: 1px;
}

.max-w-full {
max-width: 100%;
}

.max-w-xs {
max-width: 20rem;
}

.max-w-none {
max-width: none;
}

.flex-auto {
flex: 1 1 auto;
}

.flex-1 {
flex: 1 1 0%;
}

.flex-initial {
flex: 0 1 auto;
}

.flex-none {
flex: none;
}

.flex-grow {
flex-grow: 1;
}

.grow {
flex-grow: 1;
}

.border-collapse {
border-collapse: collapse;
}

.-translate-x-full {
--tw-translate-x: -100%;
transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.translate-x-full {
--tw-translate-x: 100%;
transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.rotate-0 {
--tw-rotate: 0deg;
transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.-rotate-180 {
--tw-rotate: -180deg;
transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.transform {
transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

@-webkit-keyframes ping {
75%, 100% {
transform: scale(2);
opacity: 0;
}
}

@keyframes ping {
75%, 100% {
transform: scale(2);
opacity: 0;
}
}

.animate-ping {
-webkit-animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.cursor-pointer {
cursor: pointer;
}

.cursor-not-allowed {
cursor: not-allowed;
}

.resize {
resize: both;
}

.list-none {
list-style-type: none;
}

.appearance-none {
-webkit-appearance: none;
-moz-appearance: none;
appearance: none;
}

.grid-cols-3 {
grid-template-columns: repeat(3, minmax(0, 1fr));
}

.grid-cols-1 {
grid-template-columns: repeat(1, minmax(0, 1fr));
}

.grid-cols-2 {
grid-template-columns: repeat(2, minmax(0, 1fr));
}

.grid-cols-4 {
grid-template-columns: repeat(4, minmax(0, 1fr));
}

.flex-row {
flex-direction: row;
}

.flex-row-reverse {
flex-direction: row-reverse;
}

.flex-col {
flex-direction: column;
}

.flex-wrap {
flex-wrap: wrap;
}

.place-content-center {
place-content: center;
}

.items-end {
align-items: flex-end;
}

.items-center {
align-items: center;
}

.justify-end {
justify-content: flex-end;
}

.justify-center {
justify-content: center;
}

.justify-between {
justify-content: space-between;
}

.gap-6 {
gap: 1.5rem;
}

.gap-4 {
gap: 1rem;
}

.gap-3 {
gap: 0.75rem;
}

.gap-2 {
gap: 0.5rem;
}

.gap-1 {
gap: 0.25rem;
}

.gap-10 {
gap: 2.5rem;
}

.gap-x-1 {
-moz-column-gap: 0.25rem;
column-gap: 0.25rem;
}

.space-y-4 > :not([hidden]) ~ :not([hidden]) {
--tw-space-y-reverse: 0;
margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse)));
margin-bottom: calc(1rem * var(--tw-space-y-reverse));
}

.space-x-1 > :not([hidden]) ~ :not([hidden]) {
--tw-space-x-reverse: 0;
margin-right: calc(0.25rem * var(--tw-space-x-reverse));
margin-left: calc(0.25rem * calc(1 - var(--tw-space-x-reverse)));
}

.space-x-3 > :not([hidden]) ~ :not([hidden]) {
--tw-space-x-reverse: 0;
margin-right: calc(0.75rem * var(--tw-space-x-reverse));
margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
}

.space-y-0 > :not([hidden]) ~ :not([hidden]) {
--tw-space-y-reverse: 0;
margin-top: calc(0px * calc(1 - var(--tw-space-y-reverse)));
margin-bottom: calc(0px * var(--tw-space-y-reverse));
}

.divide-y > :not([hidden]) ~ :not([hidden]) {
--tw-divide-y-reverse: 0;
border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
border-bottom-width: calc(1px * var(--tw-divide-y-reverse));
}

.divide-x > :not([hidden]) ~ :not([hidden]) {
--tw-divide-x-reverse: 0;
border-right-width: calc(1px * var(--tw-divide-x-reverse));
border-left-width: calc(1px * calc(1 - var(--tw-divide-x-reverse)));
}

.divide-solid > :not([hidden]) ~ :not([hidden]) {
border-style: solid;
}

.overflow-hidden {
overflow: hidden;
}

.overflow-x-auto {
overflow-x: auto;
}

.overflow-y-auto {
overflow-y: auto;
}

.overflow-y-scroll {
overflow-y: scroll;
}

.truncate {
overflow: hidden;
text-overflow: ellipsis;
white-space: nowrap;
}

.whitespace-normal {
white-space: normal;
}

.whitespace-nowrap {
white-space: nowrap;
}

.whitespace-pre {
white-space: pre;
}

.whitespace-pre-line {
white-space: pre-line;
}

.whitespace-pre-wrap {
white-space: pre-wrap;
}

.break-words {
overflow-wrap: break-word;
}

.break-all {
word-break: break-all;
}

.rounded {
border-radius: 0.25rem;
}

.rounded-full {
border-radius: 9999px;
}

.rounded-sm {
border-radius: 0.125rem;
}

.rounded-lg {
border-radius: 0.5rem;
}

.rounded-\[500px\] {
border-radius: 500px;
}

.rounded-t-lg {
border-top-left-radius: 0.5rem;
border-top-right-radius: 0.5rem;
}

.border {
border-width: 1px;
}

.border-0 {
border-width: 0px;
}

.border-2 {
border-width: 2px;
}

.border-t {
border-top-width: 1px;
}

.border-b-2 {
border-bottom-width: 2px;
}

.border-b {
border-bottom-width: 1px;
}

.border-solid {
border-style: solid;
}

.border-red-500 {
--tw-border-opacity: 1;
border-color: rgb(239 68 68 / var(--tw-border-opacity));
}

.border-gray-400 {
--tw-border-opacity: 1;
border-color: rgb(156 163 175 / var(--tw-border-opacity));
}

.border-sushi-600 {
--tw-border-opacity: 1;
border-color: rgb(110 157 49 / var(--tw-border-opacity));
}

.border-gray-200 {
--tw-border-opacity: 1;
border-color: rgb(229 231 235 / var(--tw-border-opacity));
}

.border-gray-100 {
--tw-border-opacity: 1;
border-color: rgb(243 244 246 / var(--tw-border-opacity));
}

.border-sushi-500 {
--tw-border-opacity: 1;
border-color: rgb(122 174 54 / var(--tw-border-opacity));
}

.border-gray-300 {
--tw-border-opacity: 1;
border-color: rgb(209 213 219 / var(--tw-border-opacity));
}

.border-sushi-200 {
--tw-border-opacity: 1;
border-color: rgb(222 235 205 / var(--tw-border-opacity));
}

.border-red-200 {
--tw-border-opacity: 1;
border-color: rgb(254 202 202 / var(--tw-border-opacity));
}

.border-transparent {
border-color: transparent;
}

.border-blue-600 {
--tw-border-opacity: 1;
border-color: rgb(37 99 235 / var(--tw-border-opacity));
}

.border-slate-200 {
--tw-border-opacity: 1;
border-color: rgb(226 232 240 / var(--tw-border-opacity));
}

.border-slate-500 {
--tw-border-opacity: 1;
border-color: rgb(100 116 139 / var(--tw-border-opacity));
}

.border-slate-600 {
--tw-border-opacity: 1;
border-color: rgb(71 85 105 / var(--tw-border-opacity));
}

.border-chelsea-cucumber-400 {
--tw-border-opacity: 1;
border-color: rgb(168 204 142 / var(--tw-border-opacity));
}

.border-l-chelsea-cucumber-400 {
--tw-border-opacity: 1;
border-left-color: rgb(168 204 142 / var(--tw-border-opacity));
}

.bg-white {
--tw-bg-opacity: 1;
background-color: rgb(255 255 255 / var(--tw-bg-opacity));
}

.bg-gray-50 {
--tw-bg-opacity: 1;
background-color: rgb(249 250 251 / var(--tw-bg-opacity));
}

.bg-gray-100 {
--tw-bg-opacity: 1;
background-color: rgb(243 244 246 / var(--tw-bg-opacity));
}

.bg-cornflower-blue-500 {
--tw-bg-opacity: 1;
background-color: rgb(52 135 225 / var(--tw-bg-opacity));
}

.bg-cornflower-blue-600 {
--tw-bg-opacity: 1;
background-color: rgb(47 122 203 / var(--tw-bg-opacity));
}

.bg-scooter-500 {
--tw-bg-opacity: 1;
background-color: rgb(1 181 217 / var(--tw-bg-opacity));
}

.bg-scooter-600 {
--tw-bg-opacity: 1;
background-color: rgb(1 163 195 / var(--tw-bg-opacity));
}

.bg-pine-green-500 {
--tw-bg-opacity: 1;
background-color: rgb(1 121 111 / var(--tw-bg-opacity));
}

.bg-pine-green-600 {
--tw-bg-opacity: 1;
background-color: rgb(1 109 100 / var(--tw-bg-opacity));
}

.bg-chelsea-cucumber-500 {
--tw-bg-opacity: 1;
background-color: rgb(131 182 93 / var(--tw-bg-opacity));
}

.bg-chelsea-cucumber-600 {
--tw-bg-opacity: 1;
background-color: rgb(118 164 84 / var(--tw-bg-opacity));
}

.bg-buttercup-500 {
--tw-bg-opacity: 1;
background-color: rgb(245 183 37 / var(--tw-bg-opacity));
}

.bg-buttercup-600 {
--tw-bg-opacity: 1;
background-color: rgb(221 165 33 / var(--tw-bg-opacity));
}

.bg-carrot-orange-500 {
--tw-bg-opacity: 1;
background-color: rgb(237 145 33 / var(--tw-bg-opacity));
}

.bg-carrot-orange-600 {
--tw-bg-opacity: 1;
background-color: rgb(213 131 30 / var(--tw-bg-opacity));
}

.bg-outrageous-orange-500 {
--tw-bg-opacity: 1;
background-color: rgb(255 93 61 / var(--tw-bg-opacity));
}

.bg-outrageous-orange-600 {
--tw-bg-opacity: 1;
background-color: rgb(230 84 55 / var(--tw-bg-opacity));
}

.bg-pomegranate-500 {
--tw-bg-opacity: 1;
background-color: rgb(227 57 26 / var(--tw-bg-opacity));
}

.bg-pomegranate-600 {
--tw-bg-opacity: 1;
background-color: rgb(204 51 23 / var(--tw-bg-opacity));
}

.bg-lilac-bush-500 {
--tw-bg-opacity: 1;
background-color: rgb(148 108 203 / var(--tw-bg-opacity));
}

.bg-lilac-bush-600 {
--tw-bg-opacity: 1;
background-color: rgb(133 97 183 / var(--tw-bg-opacity));
}

.bg-amethyst-500 {
--tw-bg-opacity: 1;
background-color: rgb(154 77 181 / var(--tw-bg-opacity));
}

.bg-amethyst-600 {
--tw-bg-opacity: 1;
background-color: rgb(139 69 163 / var(--tw-bg-opacity));
}

.bg-flush-orange-500 {
--tw-bg-opacity: 1;
background-color: rgb(255 131 13 / var(--tw-bg-opacity));
}

.bg-flush-orange-600 {
--tw-bg-opacity: 1;
background-color: rgb(230 118 12 / var(--tw-bg-opacity));
}

.bg-san-juan-500 {
--tw-bg-opacity: 1;
background-color: rgb(50 82 123 / var(--tw-bg-opacity));
}

.bg-san-juan-600 {
--tw-bg-opacity: 1;
background-color: rgb(45 74 111 / var(--tw-bg-opacity));
}

.bg-stack-500 {
--tw-bg-opacity: 1;
background-color: rgb(151 146 139 / var(--tw-bg-opacity));
}

.bg-stack-600 {
--tw-bg-opacity: 1;
background-color: rgb(136 131 125 / var(--tw-bg-opacity));
}

.bg-eastern-blue-700 {
--tw-bg-opacity: 1;
background-color: rgb(17 78 88 / var(--tw-bg-opacity));
}

.bg-eastern-blue-900 {
--tw-bg-opacity: 1;
background-color: rgb(0 0 0 / var(--tw-bg-opacity));
}

.bg-gray-300 {
--tw-bg-opacity: 1;
background-color: rgb(209 213 219 / var(--tw-bg-opacity));
}

.bg-sushi-300 {
--tw-bg-opacity: 1;
background-color: rgb(202 223 175 / var(--tw-bg-opacity));
}

.bg-red-100 {
--tw-bg-opacity: 1;
background-color: rgb(254 226 226 / var(--tw-bg-opacity));
}

.bg-amber-100 {
--tw-bg-opacity: 1;
background-color: rgb(254 243 199 / var(--tw-bg-opacity));
}

.bg-orange-100 {
--tw-bg-opacity: 1;
background-color: rgb(255 237 213 / var(--tw-bg-opacity));
}

.bg-blue-100 {
--tw-bg-opacity: 1;
background-color: rgb(219 234 254 / var(--tw-bg-opacity));
}

.bg-zinc-100 {
--tw-bg-opacity: 1;
background-color: rgb(244 244 245 / var(--tw-bg-opacity));
}

.bg-gray-700 {
--tw-bg-opacity: 1;
background-color: rgb(55 65 81 / var(--tw-bg-opacity));
}

.bg-gray-900 {
--tw-bg-opacity: 1;
background-color: rgb(17 24 39 / var(--tw-bg-opacity));
}

.bg-red-400 {
--tw-bg-opacity: 1;
background-color: rgb(248 113 113 / var(--tw-bg-opacity));
}

.bg-sushi-500 {
--tw-bg-opacity: 1;
background-color: rgb(122 174 54 / var(--tw-bg-opacity));
}

.bg-red-500 {
--tw-bg-opacity: 1;
background-color: rgb(239 68 68 / var(--tw-bg-opacity));
}

.bg-transparent {
background-color: transparent;
}

.bg-blue-600 {
--tw-bg-opacity: 1;
background-color: rgb(37 99 235 / var(--tw-bg-opacity));
}

.bg-gray-400 {
--tw-bg-opacity: 1;
background-color: rgb(156 163 175 / var(--tw-bg-opacity));
}

.bg-sushi-400 {
--tw-bg-opacity: 1;
background-color: rgb(162 198 114 / var(--tw-bg-opacity));
}

.bg-sushi-50 {
--tw-bg-opacity: 1;
background-color: rgb(248 251 245 / var(--tw-bg-opacity));
}

.bg-sushi-600 {
--tw-bg-opacity: 1;
background-color: rgb(110 157 49 / var(--tw-bg-opacity));
}

.bg-gray-800 {
--tw-bg-opacity: 1;
background-color: rgb(31 41 55 / var(--tw-bg-opacity));
}

.bg-blue-500 {
--tw-bg-opacity: 1;
background-color: rgb(59 130 246 / var(--tw-bg-opacity));
}

.bg-\[\#1e87f0\] {
--tw-bg-opacity: 1;
background-color: rgb(30 135 240 / var(--tw-bg-opacity));
}

.bg-opacity-75 {
--tw-bg-opacity: 0.75;
}

.bg-cover {
background-size: cover;
}

.bg-contain {
background-size: contain;
}

.bg-clip-padding {
background-clip: padding-box;
}

.bg-center {
background-position: center;
}

.bg-no-repeat {
background-repeat: no-repeat;
}

.fill-current {
fill: currentColor;
}

.stroke-cornflower-blue-700 {
stroke: #2765a9;
}

.stroke-scooter-700 {
stroke: #0188a3;
}

.stroke-pine-green-700 {
stroke: #015b53;
}

.stroke-chelsea-cucumber-700 {
stroke: #628946;
}

.stroke-buttercup-700 {
stroke: #b8891c;
}

.stroke-carrot-orange-700 {
stroke: #b26d19;
}

.stroke-outrageous-orange-700 {
stroke: #bf462e;
}

.stroke-pomegranate-700 {
stroke: #aa2b14;
}

.stroke-lilac-bush-700 {
stroke: #6f5198;
}

.stroke-amethyst-700 {
stroke: #743a88;
}

.stroke-flush-orange-700 {
stroke: #bf620a;
}

.stroke-san-juan-700 {
stroke: #263e5c;
}

.stroke-stack-700 {
stroke: #716e68;
}

.stroke-sushi-300 {
stroke: #cadfaf;
}

.object-contain {
-o-object-fit: contain;
object-fit: contain;
}

.p-0 {
padding: 0px;
}

.p-4 {
padding: 1rem;
}

.p-2 {
padding: 0.5rem;
}

.p-6 {
padding: 1.5rem;
}

.p-1 {
padding: 0.25rem;
}

.p-\[30px\] {
padding: 30px;
}

.p-\[15px\] {
padding: 15px;
}

.px-40 {
padding-left: 10rem;
padding-right: 10rem;
}

.py-20 {
padding-top: 5rem;
padding-bottom: 5rem;
}

.px-1 {
padding-left: 0.25rem;
padding-right: 0.25rem;
}

.py-4 {
padding-top: 1rem;
padding-bottom: 1rem;
}

.py-1 {
padding-top: 0.25rem;
padding-bottom: 0.25rem;
}

.px-3 {
padding-left: 0.75rem;
padding-right: 0.75rem;
}

.px-2 {
padding-left: 0.5rem;
padding-right: 0.5rem;
}

.px-5 {
padding-left: 1.25rem;
padding-right: 1.25rem;
}

.py-2 {
padding-top: 0.5rem;
padding-bottom: 0.5rem;
}

.px-4 {
padding-left: 1rem;
padding-right: 1rem;
}

.px-0 {
padding-left: 0px;
padding-right: 0px;
}

.py-5 {
padding-top: 1.25rem;
padding-bottom: 1.25rem;
}

.py-0 {
padding-top: 0px;
padding-bottom: 0px;
}

.py-7 {
padding-top: 1.75rem;
padding-bottom: 1.75rem;
}

.px-10 {
padding-left: 2.5rem;
padding-right: 2.5rem;
}

.px-6 {
padding-left: 1.5rem;
padding-right: 1.5rem;
}

.py-3 {
padding-top: 0.75rem;
padding-bottom: 0.75rem;
}

.px-8 {
padding-left: 2rem;
padding-right: 2rem;
}

.px-7 {
padding-left: 1.75rem;
padding-right: 1.75rem;
}

.py-2\.5 {
padding-top: 0.625rem;
padding-bottom: 0.625rem;
}

.px-2\.5 {
padding-left: 0.625rem;
padding-right: 0.625rem;
}

.py-0\.5 {
padding-top: 0.125rem;
padding-bottom: 0.125rem;
}

.py-1\.5 {
padding-top: 0.375rem;
padding-bottom: 0.375rem;
}

.px-\[15px\] {
padding-left: 15px;
padding-right: 15px;
}

.px-\[5px\] {
padding-left: 5px;
padding-right: 5px;
}

.px-\[10px\] {
padding-left: 10px;
padding-right: 10px;
}

.pr-4 {
padding-right: 1rem;
}

.pl-4 {
padding-left: 1rem;
}

.pt-4 {
padding-top: 1rem;
}

.pb-2 {
padding-bottom: 0.5rem;
}

.pt-2 {
padding-top: 0.5rem;
}

.pl-12 {
padding-left: 3rem;
}

.pt-6 {
padding-top: 1.5rem;
}

.pb-4 {
padding-bottom: 1rem;
}

.pl-8 {
padding-left: 2rem;
}

.pl-3 {
padding-left: 0.75rem;
}

.pt-0 {
padding-top: 0px;
}

.pl-\[30px\] {
padding-left: 30px;
}

.text-left {
text-align: left;
}

.text-center {
text-align: center;
}

.text-right {
text-align: right;
}

.align-top {
vertical-align: top;
}

.align-middle {
vertical-align: middle;
}

.text-9xl {
font-size: 8rem;
line-height: 1;
}

.text-2xl {
font-size: 1.5rem;
line-height: 2rem;
}

.text-base {
font-size: 1rem;
line-height: 1.5rem;
}

.text-lg {
font-size: 1.125rem;
line-height: 1.75rem;
}

.text-xs {
font-size: 0.75rem;
line-height: 1rem;
}

.text-sm {
font-size: 0.875rem;
line-height: 1.25rem;
}

.text-xl {
font-size: 1.25rem;
line-height: 1.75rem;
}

.text-3xl {
font-size: 1.875rem;
line-height: 2.25rem;
}

.text-\[0\.875rem\] {
font-size: 0.875rem;
}

.text-\[11px\] {
font-size: 11px;
}

.text-\[1\.25rem\] {
font-size: 1.25rem;
}

.text-\[1\.5rem\] {
font-size: 1.5rem;
}

.font-bold {
font-weight: 700;
}

.font-semibold {
font-weight: 600;
}

.font-medium {
font-weight: 500;
}

.font-normal {
font-weight: 400;
}

.font-light {
font-weight: 300;
}

.uppercase {
text-transform: uppercase;
}

.capitalize {
text-transform: capitalize;
}

.leading-normal {
line-height: 1.5;
}

.leading-3 {
line-height: .75rem;
}

.leading-tight {
line-height: 1.25;
}

.text-\[\#666\] {
--tw-text-opacity: 1;
color: rgb(102 102 102 / var(--tw-text-opacity));
}

.text-sushi-600 {
--tw-text-opacity: 1;
color: rgb(110 157 49 / var(--tw-text-opacity));
}

.text-gray-800 {
--tw-text-opacity: 1;
color: rgb(31 41 55 / var(--tw-text-opacity));
}

.text-gray-500 {
--tw-text-opacity: 1;
color: rgb(107 114 128 / var(--tw-text-opacity));
}

.text-\[\#f0506e\] {
--tw-text-opacity: 1;
color: rgb(240 80 110 / var(--tw-text-opacity));
}

.text-red-500 {
--tw-text-opacity: 1;
color: rgb(239 68 68 / var(--tw-text-opacity));
}

.text-cornflower-blue-50 {
--tw-text-opacity: 1;
color: rgb(245 249 254 / var(--tw-text-opacity));
}

.text-scooter-50 {
--tw-text-opacity: 1;
color: rgb(242 251 253 / var(--tw-text-opacity));
}

.text-pine-green-50 {
--tw-text-opacity: 1;
color: rgb(242 248 248 / var(--tw-text-opacity));
}

.text-chelsea-cucumber-50 {
--tw-text-opacity: 1;
color: rgb(249 251 247 / var(--tw-text-opacity));
}

.text-buttercup-50 {
--tw-text-opacity: 1;
color: rgb(255 251 244 / var(--tw-text-opacity));
}

.text-carrot-orange-50 {
--tw-text-opacity: 1;
color: rgb(254 250 244 / var(--tw-text-opacity));
}

.text-outrageous-orange-50 {
--tw-text-opacity: 1;
color: rgb(255 247 245 / var(--tw-text-opacity));
}

.text-pomegranate-50 {
--tw-text-opacity: 1;
color: rgb(254 245 244 / var(--tw-text-opacity));
}

.text-lilac-bush-50 {
--tw-text-opacity: 1;
color: rgb(250 248 252 / var(--tw-text-opacity));
}

.text-amethyst-50 {
--tw-text-opacity: 1;
color: rgb(250 246 251 / var(--tw-text-opacity));
}

.text-flush-orange-50 {
--tw-text-opacity: 1;
color: rgb(255 249 243 / var(--tw-text-opacity));
}

.text-san-juan-50 {
--tw-text-opacity: 1;
color: rgb(245 246 248 / var(--tw-text-opacity));
}

.text-stack-50 {
--tw-text-opacity: 1;
color: rgb(250 250 249 / var(--tw-text-opacity));
}

.text-white {
--tw-text-opacity: 1;
color: rgb(255 255 255 / var(--tw-text-opacity));
}

.text-gray-900 {
--tw-text-opacity: 1;
color: rgb(17 24 39 / var(--tw-text-opacity));
}

.text-blue-700 {
--tw-text-opacity: 1;
color: rgb(29 78 216 / var(--tw-text-opacity));
}

.text-blue-500 {
--tw-text-opacity: 1;
color: rgb(59 130 246 / var(--tw-text-opacity));
}

.text-orange-600 {
--tw-text-opacity: 1;
color: rgb(234 88 12 / var(--tw-text-opacity));
}

.text-gray-400 {
--tw-text-opacity: 1;
color: rgb(156 163 175 / var(--tw-text-opacity));
}

.text-gray-700 {
--tw-text-opacity: 1;
color: rgb(55 65 81 / var(--tw-text-opacity));
}

.text-red-800 {
--tw-text-opacity: 1;
color: rgb(153 27 27 / var(--tw-text-opacity));
}

.text-amber-800 {
--tw-text-opacity: 1;
color: rgb(146 64 14 / var(--tw-text-opacity));
}

.text-orange-800 {
--tw-text-opacity: 1;
color: rgb(154 52 18 / var(--tw-text-opacity));
}

.text-blue-800 {
--tw-text-opacity: 1;
color: rgb(30 64 175 / var(--tw-text-opacity));
}

.text-zinc-800 {
--tw-text-opacity: 1;
color: rgb(39 39 42 / var(--tw-text-opacity));
}

.text-sushi-50 {
--tw-text-opacity: 1;
color: rgb(248 251 245 / var(--tw-text-opacity));
}

.text-blue-600 {
--tw-text-opacity: 1;
color: rgb(37 99 235 / var(--tw-text-opacity));
}

.text-orange-700 {
--tw-text-opacity: 1;
color: rgb(194 65 12 / var(--tw-text-opacity));
}

.text-red-900 {
--tw-text-opacity: 1;
color: rgb(127 29 29 / var(--tw-text-opacity));
}

.text-red-700 {
--tw-text-opacity: 1;
color: rgb(185 28 28 / var(--tw-text-opacity));
}

.text-\[\#333333\] {
--tw-text-opacity: 1;
color: rgb(51 51 51 / var(--tw-text-opacity));
}

.text-\[\#999\] {
--tw-text-opacity: 1;
color: rgb(153 153 153 / var(--tw-text-opacity));
}

.underline {
text-decoration-line: underline;
}

.no-underline {
text-decoration-line: none;
}

.decoration-0 {
text-decoration-thickness: 0px;
}

.opacity-50 {
opacity: 0.5;
}

.opacity-75 {
opacity: 0.75;
}

.shadow-md {
--tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
--tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.outline-none {
outline: 2px solid transparent;
outline-offset: 2px;
}

.outline {
outline-style: solid;
}

.blur {
--tw-blur: blur(8px);
filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.drop-shadow-md {
--tw-drop-shadow: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06));
filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.drop-shadow {
--tw-drop-shadow: drop-shadow(0 1px 2px rgb(0 0 0 / 0.1)) drop-shadow(0 1px 1px rgb(0 0 0 / 0.06));
filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.grayscale {
--tw-grayscale: grayscale(100%);
filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.filter {
filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.transition {
transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-backdrop-filter;
transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-backdrop-filter;
transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
transition-duration: 150ms;
}

.transition-all {
transition-property: all;
transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
transition-duration: 150ms;
}

.duration-300 {
transition-duration: 300ms;
}

.duration-200 {
transition-duration: 200ms;
}

.ease-in {
transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
}

.ease-in-out {
transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.text-xxs {
font-size : 0.5rem;
line-height: 1rem;
}

.uk-text-danger {
--tw-text-opacity: 1;
color: rgb(240 80 110 / var(--tw-text-opacity));
}

.uk-text-bold {
font-weight: 700;
}

input[aria-invalid="true"] {
border-width: 1px;
--tw-border-opacity: 1;
border-color: rgb(239 68 68 / var(--tw-border-opacity));
--tw-text-opacity: 1;
color: rgb(239 68 68 / var(--tw-text-opacity));
}

[v-cloak] {
display: none;
}

/**************************\
Basic Modal Styles
\**************************/

.modal {
font-family: -apple-system,BlinkMacSystemFont,avenir next,avenir,helvetica neue,helvetica,ubuntu,roboto,noto,segoe ui,arial,sans-serif;
}

.modal__overlay {
position: fixed;
top: 0;
left: 0;
right: 0;
bottom: 0;
background: rgba(0,0,0,0.6);
display: flex;
justify-content: center;
align-items: center;
}

.modal__container {
background-color: #fff;
padding: 30px;
max-width: 500px;
max-height: 100vh;
border-radius: 4px;
overflow-y: auto;
box-sizing: border-box;
}

.modal_large_container {
background-color: #fff;
padding: 30px;
width: 100vw;
height: 100vh;
border-radius: 4px;
overflow-y: auto;
box-sizing: border-box;
}

.modal__header {
display: flex;
justify-content: space-between;
align-items: center;
}

.modal__title {
margin-top: 0;
margin-bottom: 0;
font-weight: 600;
font-size: 1.25rem;
line-height: 1.25;
color: #00449e;
box-sizing: border-box;
}

.modal__close {
background: transparent;
border: 0;
}

.modal__header .modal__close:before {
content: "\2715";
}

.modal__content {
margin-top: 2rem;
margin-bottom: 2rem;
line-height: 1.5;
color: rgba(0,0,0,.8);
}

.modal_large_content {
line-height: 1.5;
color: rgba(0,0,0,.8);
}

.modal__btn {
font-size: .875rem;
padding-left: 1rem;
padding-right: 1rem;
padding-top: .5rem;
padding-bottom: .5rem;
background-color: #e6e6e6;
color: rgba(0,0,0,.8);
border-radius: .25rem;
border-style: none;
border-width: 0;
cursor: pointer;
-webkit-appearance: button;
text-transform: none;
overflow: visible;
line-height: 1.15;
margin: 0;
will-change: transform;
-moz-osx-font-smoothing: grayscale;
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
transform: translateZ(0);
transition: transform .25s ease-out;
}

.modal__btn:focus, .modal__btn:hover {
transform: scale(1.05);
}

.modal__btn-primary {
background-color: #00449e;
color: #fff;
}

/**************************\
Demo Animation Style
\**************************/

@-webkit-keyframes mmfadeIn {
from {
opacity: 0;
}

to {
opacity: 1;
}
}

@keyframes mmfadeIn {
from {
opacity: 0;
}

to {
opacity: 1;
}
}

@-webkit-keyframes mmfadeOut {
from {
opacity: 1;
}

to {
opacity: 0;
}
}

@keyframes mmfadeOut {
from {
opacity: 1;
}

to {
opacity: 0;
}
}

@-webkit-keyframes mmslideIn {
from {
transform: translateY(15%);
}

to {
transform: translateY(0);
}
}

@keyframes mmslideIn {
from {
transform: translateY(15%);
}

to {
transform: translateY(0);
}
}

@-webkit-keyframes mmslideOut {
from {
transform: translateY(0);
}

to {
transform: translateY(-10%);
}
}

@keyframes mmslideOut {
from {
transform: translateY(0);
}

to {
transform: translateY(-10%);
}
}

.micromodal-slide {
display: none;
}

.micromodal-slide.is-open {
display: block;
}

.micromodal-slide .modal__container,
.micromodal-slide .modal__overlay {
will-change: transform;
}

.list-enter-active, .list-leave-active {
transition: all 0.5s;
}

.list-enter, .list-leave-to /* .list-leave-active for below version 2.1.8 */ {
opacity: 0;
transform: translateX(30px);
}

.swal2-html-container {
white-space: pre;
}

.inputChange {
background-color: rgb(255, 204, 153);
}

.checked\:border-blue-600:checked {
--tw-border-opacity: 1;
border-color: rgb(37 99 235 / var(--tw-border-opacity));
}

.checked\:bg-blue-600:checked {
--tw-bg-opacity: 1;
background-color: rgb(37 99 235 / var(--tw-bg-opacity));
}

.hover\:border-gray-400:hover {
--tw-border-opacity: 1;
border-color: rgb(156 163 175 / var(--tw-border-opacity));
}

.hover\:border-sushi-700:hover {
--tw-border-opacity: 1;
border-color: rgb(92 131 41 / var(--tw-border-opacity));
}

.hover\:border-red-700:hover {
--tw-border-opacity: 1;
border-color: rgb(185 28 28 / var(--tw-border-opacity));
}

.hover\:border-gray-300:hover {
--tw-border-opacity: 1;
border-color: rgb(209 213 219 / var(--tw-border-opacity));
}

.hover\:bg-sushi-50:hover {
--tw-bg-opacity: 1;
background-color: rgb(248 251 245 / var(--tw-bg-opacity));
}

.hover\:bg-eastern-blue-800:hover {
--tw-bg-opacity: 1;
background-color: rgb(8 36 41 / var(--tw-bg-opacity));
}

.hover\:bg-blue-400:hover {
--tw-bg-opacity: 1;
background-color: rgb(96 165 250 / var(--tw-bg-opacity));
}

.hover\:bg-sushi-400:hover {
--tw-bg-opacity: 1;
background-color: rgb(162 198 114 / var(--tw-bg-opacity));
}

.hover\:bg-red-400:hover {
--tw-bg-opacity: 1;
background-color: rgb(248 113 113 / var(--tw-bg-opacity));
}

.hover\:bg-gray-200:hover {
--tw-bg-opacity: 1;
background-color: rgb(229 231 235 / var(--tw-bg-opacity));
}

.hover\:bg-blue-600:hover {
--tw-bg-opacity: 1;
background-color: rgb(37 99 235 / var(--tw-bg-opacity));
}

.hover\:bg-blue-700:hover {
--tw-bg-opacity: 1;
background-color: rgb(29 78 216 / var(--tw-bg-opacity));
}

.hover\:text-white:hover {
--tw-text-opacity: 1;
color: rgb(255 255 255 / var(--tw-text-opacity));
}

.hover\:text-gray-600:hover {
--tw-text-opacity: 1;
color: rgb(75 85 99 / var(--tw-text-opacity));
}

.hover\:text-gray-800:hover {
--tw-text-opacity: 1;
color: rgb(31 41 55 / var(--tw-text-opacity));
}

.hover\:text-gray-700:hover {
--tw-text-opacity: 1;
color: rgb(55 65 81 / var(--tw-text-opacity));
}

.hover\:underline:hover {
text-decoration-line: underline;
}

.focus\:border-blue-600:focus {
--tw-border-opacity: 1;
border-color: rgb(37 99 235 / var(--tw-border-opacity));
}

.focus\:bg-white:focus {
--tw-bg-opacity: 1;
background-color: rgb(255 255 255 / var(--tw-bg-opacity));
}

.focus\:text-gray-700:focus {
--tw-text-opacity: 1;
color: rgb(55 65 81 / var(--tw-text-opacity));
}

.focus\:shadow-none:focus {
--tw-shadow: 0 0 #0000;
--tw-shadow-colored: 0 0 #0000;
box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.focus\:shadow-md:focus {
--tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
--tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.focus\:outline-none:focus {
outline: 2px solid transparent;
outline-offset: 2px;
}

.focus\:ring-0:focus {
--tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
--tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(0px + var(--tw-ring-offset-width)) var(--tw-ring-color);
box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.focus\:ring-4:focus {
--tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
--tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.disabled\:border-slate-200:disabled {
--tw-border-opacity: 1;
border-color: rgb(226 232 240 / var(--tw-border-opacity));
}

.disabled\:bg-slate-50:disabled {
--tw-bg-opacity: 1;
background-color: rgb(248 250 252 / var(--tw-bg-opacity));
}

.disabled\:text-slate-500:disabled {
--tw-text-opacity: 1;
color: rgb(100 116 139 / var(--tw-text-opacity));
}

.disabled\:shadow-none:disabled {
--tw-shadow: 0 0 #0000;
--tw-shadow-colored: 0 0 #0000;
box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

@media (min-width: 640px) {
.sm\:-mx-6 {
margin-left: -1.5rem;
margin-right: -1.5rem;
}

.sm\:block {
display: block;
}

.sm\:px-6 {
padding-left: 1.5rem;
padding-right: 1.5rem;
}

.sm\:px-4 {
padding-left: 1rem;
padding-right: 1rem;
}
}

@media (min-width: 768px) {
.md\:right-\[20px\] {
right: 20px;
}

.md\:left-auto {
left: auto;
}

.md\:my-0 {
margin-top: 0px;
margin-bottom: 0px;
}

.md\:mb-0 {
margin-bottom: 0px;
}

.md\:flex {
display: flex;
}

.md\:grid {
display: grid;
}

.md\:h-\[70vh\] {
height: 70vh;
}

.md\:h-44 {
height: 11rem;
}

.md\:w-\[400px\] {
width: 400px;
}

.md\:w-2\/3 {
width: 66.666667%;
}

.md\:w-44 {
width: 11rem;
}

.md\:w-1\/6 {
width: 16.666667%;
}

.md\:grid-cols-4 {
grid-template-columns: repeat(4, minmax(0, 1fr));
}

.md\:grid-cols-2 {
grid-template-columns: repeat(2, minmax(0, 1fr));
}

.md\:flex-row {
flex-direction: row;
}

.md\:gap-10 {
gap: 2.5rem;
}

.md\:gap-6 {
gap: 1.5rem;
}

.md\:space-x-3 > :not([hidden]) ~ :not([hidden]) {
--tw-space-x-reverse: 0;
margin-right: calc(0.75rem * var(--tw-space-x-reverse));
margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
}

.md\:p-\[40px\] {
padding: 40px;
}

.md\:px-10 {
padding-left: 2.5rem;
padding-right: 2.5rem;
}

.md\:text-3xl {
font-size: 1.875rem;
line-height: 2.25rem;
}

.md\:text-lg {
font-size: 1.125rem;
line-height: 1.75rem;
}
}

@media (min-width: 1024px) {
.lg\:mx-4 {
margin-left: 1rem;
margin-right: 1rem;
}

.lg\:-mx-8 {
margin-left: -2rem;
margin-right: -2rem;
}

.lg\:mb-0 {
margin-bottom: 0px;
}

.lg\:mt-0 {
margin-top: 0px;
}

.lg\:block {
display: block;
}

.lg\:flex {
display: flex;
}

.lg\:h-auto {
height: auto;
}

.lg\:w-1\/3 {
width: 33.333333%;
}

.lg\:w-3\/4 {
width: 75%;
}

.lg\:w-1\/4 {
width: 25%;
}

.lg\:w-1\/2 {
width: 50%;
}

.lg\:w-1\/6 {
width: 16.666667%;
}

.lg\:w-4\/5 {
width: 80%;
}

.lg\:w-5\/6 {
width: 83.333333%;
}

.lg\:w-1\/5 {
width: 20%;
}

.lg\:w-2\/3 {
width: 66.666667%;
}

.lg\:w-48 {
width: 12rem;
}

.lg\:w-2\/4 {
width: 50%;
}

.lg\:w-3\/5 {
width: 60%;
}

.lg\:w-2\/5 {
width: 40%;
}

.lg\:w-auto {
width: auto;
}

.lg\:flex-1 {
flex: 1 1 0%;
}

.lg\:space-y-0 > :not([hidden]) ~ :not([hidden]) {
--tw-space-y-reverse: 0;
margin-top: calc(0px * calc(1 - var(--tw-space-y-reverse)));
margin-bottom: calc(0px * var(--tw-space-y-reverse));
}

.lg\:divide-x > :not([hidden]) ~ :not([hidden]) {
--tw-divide-x-reverse: 0;
border-right-width: calc(1px * var(--tw-divide-x-reverse));
border-left-width: calc(1px * calc(1 - var(--tw-divide-x-reverse)));
}

.lg\:whitespace-pre {
white-space: pre;
}

.lg\:px-4 {
padding-left: 1rem;
padding-right: 1rem;
}

.lg\:px-8 {
padding-left: 2rem;
padding-right: 2rem;
}

.lg\:px-7 {
padding-left: 1.75rem;
padding-right: 1.75rem;
}

.lg\:pt-0 {
padding-top: 0px;
}
}

@media (min-width: 1280px) {
.xl\:mb-0 {
margin-bottom: 0px;
}
}

.bg-purple-200 {
--tw-bg-opacity: 1;
background-color: rgb(233 213 255 / var(--tw-bg-opacity));
}

.text-purple-900 {
--tw-text-opacity: 1;
color: rgb(88 28 135 / var(--tw-text-opacity));
}

.min-w-250 {
min-width: 250px;
}

.float-none {
float: none;
}