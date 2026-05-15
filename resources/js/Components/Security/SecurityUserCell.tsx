export default function SecurityUserCell({
    name,
    email,
    role,
}: {
    name?: string | null;
    email?: string | null;
    role?: string | null;
}) {
    return (
        <div className="min-w-[10rem] space-y-0.5">
            <p className="font-medium text-navy-900">{name ?? 'Sistema'}</p>
            {email ? <p className="text-xs text-plomo">{email}</p> : null}
            {role ? (
                <span className="inline-flex rounded-md bg-navy-50 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-navy-800">
                    {role}
                </span>
            ) : null}
        </div>
    );
}
