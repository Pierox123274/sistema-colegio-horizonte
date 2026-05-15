type Link = { url: string | null; label: string; active: boolean };

export default function SecurityPagination({
    links,
    total,
}: {
    links?: Link[];
    total?: number;
}) {
    if (!links?.length) {
        return null;
    }

    return (
        <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            {total !== undefined ? (
                <p className="text-xs text-plomo">
                    {total} registro{total === 1 ? '' : 's'} en total
                </p>
            ) : null}
            <nav className="flex flex-wrap gap-1" aria-label="Paginación">
                {links.map((link, i) =>
                    link.url ? (
                        <a
                            key={`${link.label}-${i}`}
                            href={link.url}
                            className={`rounded-lg px-3 py-1.5 text-xs font-medium ${
                                link.active
                                    ? 'bg-navy-900 text-white'
                                    : 'bg-navy-50 text-navy-900 hover:bg-navy-100'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ) : (
                        <span
                            key={`${link.label}-${i}`}
                            className="rounded-lg px-3 py-1.5 text-xs text-plomo"
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ),
                )}
            </nav>
        </div>
    );
}
