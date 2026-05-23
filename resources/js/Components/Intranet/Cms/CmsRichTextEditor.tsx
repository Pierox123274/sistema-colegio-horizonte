type CmsRichTextEditorProps = {
    label: string;
    value: string;
    onChange: (html: string) => void;
    error?: string;
};

/** Editor HTML (preparado para Tiptap en iteración siguiente). */
export function CmsRichTextEditor({ label, value, onChange, error }: CmsRichTextEditorProps) {
    return (
        <div>
            <label className="text-sm font-medium text-navy-800">{label}</label>
            <textarea
                value={value}
                onChange={(e) => onChange(e.target.value)}
                rows={12}
                className="mt-1.5 w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500"
                placeholder="<p>Contenido en HTML…</p>"
            />
            <p className="mt-1 text-xs text-plomo">Puedes usar HTML básico: p, h2, ul, li, a, strong.</p>
            {error ? <p className="mt-1 text-sm text-red-600">{error}</p> : null}
        </div>
    );
}
