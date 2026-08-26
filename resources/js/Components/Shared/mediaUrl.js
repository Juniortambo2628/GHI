export default function mediaUrl(path) {
    if (!path) return '';
    if (/^https?:\/\//.test(path) || path.startsWith('/')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    if (path.startsWith('images/') || path.startsWith('documents/') || path.startsWith('files/')) return `/storage/${path}`;
    return `/${path}`;
}
