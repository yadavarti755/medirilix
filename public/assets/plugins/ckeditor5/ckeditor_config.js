/**
 * This configuration was generated using the CKEditor 5 Builder. You can modify it anytime using this link:
 * https://ckeditor.com/ckeditor-5/builder/#installation/NoJgNARCB0DMcUgRgAwE4UHY1IBy6QBZYBWFXANli0MKVUMotsxULVh0sMQgFMAdohRhgSMOPEjJAXWQoQAQwBGJXBBlA===
 */

import {
    ClassicEditor,
    Alignment,
    Autoformat,
    AutoImage,
    AutoLink,
    Autosave,
    BalloonToolbar,
    Base64UploadAdapter,
    BlockQuote,
    BlockToolbar,
    Bold,
    Bookmark,
    Code,
    CodeBlock,
    Emoji,
    Essentials,
    FindAndReplace,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    FullPage,
    GeneralHtmlSupport,
    Heading,
    Highlight,
    HorizontalLine,
    HtmlComment,
    HtmlEmbed,
    ImageBlock,
    ImageCaption,
    ImageEditing,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    ImageUtils,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    Markdown,
    MediaEmbed,
    Mention,
    PageBreak,
    Paragraph,
    PasteFromMarkdownExperimental,
    PasteFromOffice,
    RemoveFormat,
    ShowBlocks,
    SourceEditing,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Style,
    Subscript,
    Superscript,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextPartLanguage,
    TextTransformation,
    Title,
    TodoList,
    Underline,
    WordCount,
} from "ckeditor5";

/**
 * Create a free account with a trial: https://portal.ckeditor.com/checkout?plan=free
 */
const LICENSE_KEY = "GPL"; // or <YOUR_LICENSE_KEY>.

const editorConfig = {
    toolbar: {
        items: [
            "sourceEditing",
            "showBlocks",
            "findAndReplace",
            "textPartLanguage",
            "|",
            "heading",
            "style",
            "|",
            "fontSize",
            "fontFamily",
            "fontColor",
            "fontBackgroundColor",
            "|",
            "bold",
            "italic",
            "underline",
            "strikethrough",
            "subscript",
            "superscript",
            "code",
            "removeFormat",
            "|",
            "emoji",
            "specialCharacters",
            "horizontalLine",
            "pageBreak",
            "link",
            "bookmark",
            "insertImage",
            "insertImageViaUrl",
            "mediaEmbed",
            "insertTable",
            "highlight",
            "blockQuote",
            "codeBlock",
            "htmlEmbed",
            "|",
            "alignment",
            "|",
            "bulletedList",
            "numberedList",
            "todoList",
            "outdent",
            "indent",
        ],
        shouldNotGroupWhenFull: true,
    },
    plugins: [
        Alignment,
        Autoformat,
        AutoImage,
        AutoLink,
        Autosave,
        BalloonToolbar,
        Base64UploadAdapter,
        BlockQuote,
        BlockToolbar,
        Bold,
        Bookmark,
        Code,
        CodeBlock,
        Emoji,
        Essentials,
        FindAndReplace,
        FontBackgroundColor,
        FontColor,
        FontFamily,
        FontSize,
        FullPage,
        GeneralHtmlSupport,
        Heading,
        Highlight,
        HorizontalLine,
        HtmlComment,
        HtmlEmbed,
        ImageBlock,
        ImageCaption,
        ImageEditing,
        ImageInline,
        ImageInsert,
        ImageInsertViaUrl,
        ImageResize,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        ImageUpload,
        ImageUtils,
        Indent,
        IndentBlock,
        Italic,
        Link,
        LinkImage,
        List,
        ListProperties,
        Markdown,
        MediaEmbed,
        Mention,
        PageBreak,
        Paragraph,
        PasteFromMarkdownExperimental,
        PasteFromOffice,
        RemoveFormat,
        ShowBlocks,
        SourceEditing,
        SpecialCharacters,
        SpecialCharactersArrows,
        SpecialCharactersCurrency,
        SpecialCharactersEssentials,
        SpecialCharactersLatin,
        SpecialCharactersMathematical,
        SpecialCharactersText,
        Strikethrough,
        Style,
        Subscript,
        Superscript,
        Table,
        TableCaption,
        TableCellProperties,
        TableColumnResize,
        TableProperties,
        TableToolbar,
        TextPartLanguage,
        TextTransformation,
        Title,
        TodoList,
        Underline,
        WordCount,
    ],
    balloonToolbar: [
        "bold",
        "italic",
        "|",
        "link",
        "insertImage",
        "|",
        "bulletedList",
        "numberedList",
    ],
    blockToolbar: [
        "fontSize",
        "fontColor",
        "fontBackgroundColor",
        "|",
        "bold",
        "italic",
        "|",
        "link",
        "insertImage",
        "insertTable",
        "|",
        "bulletedList",
        "numberedList",
        "outdent",
        "indent",
    ],
    fontFamily: {
        supportAllValues: true,
    },
    fontSize: {
        options: [10, 12, 14, "default", 18, 20, 22],
        supportAllValues: true,
    },
    heading: {
        options: [
            {
                model: "paragraph",
                title: "Paragraph",
                class: "ck-heading_paragraph",
            },
            {
                model: "heading1",
                view: "h1",
                title: "Heading 1",
                class: "ck-heading_heading1",
            },
            {
                model: "heading2",
                view: "h2",
                title: "Heading 2",
                class: "ck-heading_heading2",
            },
            {
                model: "heading3",
                view: "h3",
                title: "Heading 3",
                class: "ck-heading_heading3",
            },
            {
                model: "heading4",
                view: "h4",
                title: "Heading 4",
                class: "ck-heading_heading4",
            },
            {
                model: "heading5",
                view: "h5",
                title: "Heading 5",
                class: "ck-heading_heading5",
            },
            {
                model: "heading6",
                view: "h6",
                title: "Heading 6",
                class: "ck-heading_heading6",
            },
        ],
    },
    htmlSupport: {
        allow: [
            {
                name: /^.*$/,
                styles: true,
                attributes: true,
                classes: true,
            },
        ],
    },
    image: {
        toolbar: [
            "toggleImageCaption",
            "imageTextAlternative",
            "|",
            "imageStyle:inline",
            "imageStyle:wrapText",
            "imageStyle:breakText",
            "|",
            "resizeImage",
        ],
    },
    initialData: "",
    licenseKey: LICENSE_KEY,
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: "https://",
        decorators: {
            toggleDownloadable: {
                mode: "manual",
                label: "Downloadable",
                attributes: {
                    download: "file",
                },
            },
        },
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true,
        },
    },
    mention: {
        feeds: [
            {
                marker: "@",
                feed: [
                    /* See: https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html */
                ],
            },
        ],
    },
    menuBar: {
        isVisible: false,
    },
    placeholder: "Type or paste your content here!",
    style: {
        definitions: [
            {
                name: "Article category",
                element: "h3",
                classes: ["category"],
            },
            {
                name: "Title",
                element: "h2",
                classes: ["document-title"],
            },
            {
                name: "Subtitle",
                element: "h3",
                classes: ["document-subtitle"],
            },
            {
                name: "Info box",
                element: "p",
                classes: ["info-box"],
            },
            {
                name: "Side quote",
                element: "blockquote",
                classes: ["side-quote"],
            },
            {
                name: "Marker",
                element: "span",
                classes: ["marker"],
            },
            {
                name: "Spoiler",
                element: "span",
                classes: ["spoiler"],
            },
            {
                name: "Code (dark)",
                element: "pre",
                classes: ["fancy-code", "fancy-code-dark"],
            },
            {
                name: "Code (bright)",
                element: "pre",
                classes: ["fancy-code", "fancy-code-bright"],
            },
        ],
    },
    table: {
        contentToolbar: [
            "tableColumn",
            "tableRow",
            "mergeTableCells",
            "tableProperties",
            "tableCellProperties",
        ],
    },
    removePlugins: ["Title"],
};

window.editors = [];
$(document).ready(function () {
    // Check if the element exists
    if ($("#page-editor").length) {
        // Loop through each element with the class 'custom-editor'
        $("#page-editor").each(function (index) {
            ClassicEditor.create(this, editorConfig)
                .then((editor) => {
                    window.editors.push({
                        editor,
                        name: $(this).attr("name"),
                        id: $(this).attr("id"),
                    });

                    // Set the height dynamically or perform other operations
                    editor.editing.view.change((writer) => {
                        writer.setStyle(
                            "min-height",
                            "300px",
                            editor.editing.view.document.getRoot()
                        );
                        writer.setStyle(
                            "max-height",
                            "400px",
                            editor.editing.view.document.getRoot()
                        );
                    });

                    if ($("#page-editor").val() !== "") {
                        editor.data.set($("#page-editor").val());
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        });
    }

    // Check if the element exists
    if ($("#hi-page-editor").length) {
        // Loop through each element with the class 'custom-editor'
        $("#hi-page-editor").each(function (index) {
            ClassicEditor.create(this, editorConfig)
                .then((editor) => {
                    window.editors.push({
                        editor,
                        name: $(this).attr("name"),
                        id: $(this).attr("id"),
                    });

                    // Set the height dynamically or perform other operations
                    editor.editing.view.change((writer) => {
                        writer.setStyle(
                            "min-height",
                            "300px",
                            editor.editing.view.document.getRoot()
                        );
                        writer.setStyle(
                            "max-height",
                            "400px",
                            editor.editing.view.document.getRoot()
                        );
                    });

                    if ($("#hi-page-editor").val() !== "") {
                        editor.data.set($("#hi-page-editor").val());
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        });
    }
});
