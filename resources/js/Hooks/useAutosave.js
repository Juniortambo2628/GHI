import { useState, useEffect, useRef, useCallback } from 'react';
import axios from 'axios';

export default function useAutosave({ formKey, data, enabled = true, debounceMs = 30000 }) {
    const [saveStatus, setSaveStatus] = useState(null);
    const [hasUnsavedChanges, setHasUnsavedChanges] = useState(false);
    const [lastSaved, setLastSaved] = useState(null);
    const [restoredData, setRestoredData] = useState(null);
    const timerRef = useRef(null);
    const dataRef = useRef(data);
    const mountedRef = useRef(false);

    dataRef.current = data;

    const saveDraft = useCallback(async () => {
        if (!formKey || !enabled) return;
        try {
            setSaveStatus({ state: 'saving' });
            await axios.post('/admin/drafts', { form_key: formKey, data: JSON.stringify(dataRef.current) });
            setSaveStatus({ state: 'saved', message: 'Draft saved' });
            setHasUnsavedChanges(false);
            setLastSaved(new Date());
        } catch {
            setSaveStatus({ state: 'error' });
        }
    }, [formKey, enabled]);

    const loadDraft = useCallback(async () => {
        if (!formKey || !enabled) return null;
        try {
            const res = await axios.get('/admin/drafts', { params: { form_key: formKey } });
            if (res.data?.data) {
                const parsed = typeof res.data.data === 'string' ? JSON.parse(res.data.data) : res.data.data;
                setRestoredData(parsed);
                return parsed;
            }
        } catch {}
        return null;
    }, [formKey, enabled]);

    const deleteDraft = useCallback(async () => {
        if (!formKey) return;
        try {
            await axios.delete('/admin/drafts', { data: { form_key: formKey } });
        } catch {}
    }, [formKey]);

    useEffect(() => {
        if (!mountedRef.current) {
            mountedRef.current = true;
            return;
        }
        setHasUnsavedChanges(true);
        clearTimeout(timerRef.current);
        timerRef.current = setTimeout(saveDraft, debounceMs);
        return () => clearTimeout(timerRef.current);
    }, [data, saveDraft, debounceMs]);

    useEffect(() => {
        return () => clearTimeout(timerRef.current);
    }, []);

    return {
        saveStatus,
        hasUnsavedChanges,
        lastSaved,
        restoredData,
        saveNow: saveDraft,
        loadDraft,
        deleteDraft,
    };
}
