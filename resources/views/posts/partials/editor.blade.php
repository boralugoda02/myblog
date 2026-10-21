<style>
    .ck-editor__editable_inline { 
        min-height: 340px; 
    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
<script>
(function () {
    const CSRF = @json(csrf_token());
    const UPLOAD_URL = @json(route('editor.upload'));

    class BlogUploadAdapter {
        constructor(loader) { 
            this.loader = loader; 
        }

        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const xhr = this.xhr = new XMLHttpRequest();
                xhr.open('POST', UPLOAD_URL, true);
                xhr.responseType = 'json';
                xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.addEventListener('error', () => reject('Image upload failed.'));
                xhr.addEventListener('abort', () => reject());
                xhr.addEventListener('load', () => {
                    const res = xhr.response;
                    if (xhr.status >= 200 && xhr.status < 300 && res && res.url) {
                        return resolve({ default: res.url });
                    }
                    reject((res && (res.errors?.upload?.[0] || res.message)) || 'Image upload failed.');
                });

                const data = new FormData();
                data.append('upload', file);
                xhr.send(data);
            }));
        }

        abort() { 
            if (this.xhr) this.xhr.abort(); 
        }
    }

    function BlogUploadPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = loader => new BlogUploadAdapter(loader);
    }

    CKEDITOR.ClassicEditor.create(document.querySelector('#editor'), {
        extraPlugins: [BlogUploadPlugin],

        removePlugins: [
            'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
            'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
            'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
            'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
            'PasteFromOfficeEnhanced', 'CaseChange', 'ExportPdf', 'ExportWord', 'ImportWord',
            'MultiLevelList',
        ],

        toolbar: {
            items: [
                'undo', 'redo', '|', 'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                'alignment', '|',
                'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                'link', 'uploadImage', 'insertTable', 'blockQuote', 'codeBlock', 'horizontalLine', '|',
                'removeFormat',
            ],
            shouldNotGroupWhenFull: true,
        },

        image: {
            toolbar: [
                'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                'toggleImageCaption', 'imageTextAlternative', '|',
                'linkImage', '|', 'resizeImage',
            ],
        },

        placeholder: 'Write your blog content here...',
    }).catch(error => console.error('CKEditor failed to load:', error));
})();
</script>