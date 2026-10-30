import Trix from 'trix';
import 'trix/dist/trix.css';

document.addEventListener('trix-attachment-add', (event) => {
    const { attachment } = event;
    const file = attachment.file;

    if (!file) {
        return;
    }

    attachment.setUploadProgress(1);

    const body = new FormData();
    body.append('file', file);

    fetch('/kirim-artikel/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
        body,
    })
        .then((response) => (response.ok ? response.json() : Promise.reject()))
        .then(({ url }) => {
            // Tanpa href: gambar tidak dibungkus link
            attachment.setAttributes({ url });
            attachment.setUploadProgress(100);
        })
        .catch(() => attachment.remove());
});
