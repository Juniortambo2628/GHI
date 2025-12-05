/**
 * Rich Text Editor Service using Quill
 * Global Harmony Initiative Website
 */

import Quill from 'quill';
import 'quill/dist/quill.snow.css';

/**
 * Initialize Quill editor
 */
export function initEditor(container, options = {}) {
  const defaultOptions = {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'font': [] }],
        [{ 'size': [] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'script': 'sub' }, { 'script': 'super' }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'align': [] }],
        ['link', 'image', 'video'],
        ['blockquote', 'code-block'],
        ['clean'],
      ],
    },
    placeholder: options.placeholder || 'Start typing...',
    readOnly: options.readOnly || false,
  };

  const mergedOptions = {
    ...defaultOptions,
    ...options,
  };

  return new Quill(container, mergedOptions);
}

/**
 * Get editor content as HTML
 */
export function getEditorContent(editor) {
  if (!editor) return '';
  return editor.root.innerHTML;
}

/**
 * Get editor content as text
 */
export function getEditorText(editor) {
  if (!editor) return '';
  return editor.getText();
}

/**
 * Get editor content as Delta (Quill's internal format)
 */
export function getEditorDelta(editor) {
  if (!editor) return null;
  return editor.getContents();
}

/**
 * Set editor content from HTML
 */
export function setEditorContent(editor, html) {
  if (!editor) return;
  editor.root.innerHTML = html;
}

/**
 * Set editor content from Delta
 */
export function setEditorDelta(editor, delta) {
  if (!editor) return;
  editor.setContents(delta);
}

/**
 * Set editor content from text
 */
export function setEditorText(editor, text) {
  if (!editor) return;
  editor.setText(text);
}

/**
 * Enable/disable editor
 */
export function setEditorEnabled(editor, enabled) {
  if (!editor) return;
  editor.enable(enabled);
}

/**
 * Make editor read-only
 */
export function setEditorReadOnly(editor, readOnly) {
  if (!editor) return;
  editor.enable(!readOnly);
}

export default {
  init: initEditor,
  getContent: getEditorContent,
  getText: getEditorText,
  getDelta: getEditorDelta,
  setContent: setEditorContent,
  setDelta: setEditorDelta,
  setText: setEditorText,
  setEnabled: setEditorEnabled,
  setReadOnly: setEditorReadOnly,
};

