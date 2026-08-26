const allowedTags = new Set(['P', 'BR', 'STRONG', 'EM', 'U', 'A', 'UL', 'OL', 'LI', 'BLOCKQUOTE', 'H1', 'H2', 'H3']);
const allowedAttributes = new Set(['href', 'target', 'rel']);

export default function sanitizeHtml(value = '') {
    if (typeof window === 'undefined' || typeof DOMParser === 'undefined') return '';
    const document = new DOMParser().parseFromString(value, 'text/html');
    document.body.querySelectorAll('*').forEach(element => {
        if (!allowedTags.has(element.tagName)) {
            element.replaceWith(...Array.from(element.childNodes));
            return;
        }
        Array.from(element.attributes).forEach(attribute => {
            if (!allowedAttributes.has(attribute.name) || (attribute.name === 'href' && !/^https?:\/\//i.test(attribute.value))) element.removeAttribute(attribute.name);
        });
        if (element.tagName === 'A') {
            element.setAttribute('rel', 'noopener noreferrer');
            if (element.getAttribute('target') !== '_blank') element.removeAttribute('target');
        }
    });
    return document.body.innerHTML;
}
