import { Reveal } from '@/Components/Public/Premium/Reveal';

export type GalleryItem = {
    id: string;
    title: string;
    category: string;
    image: string;
    span?: string;
};

type PublicGalleryGridProps = {
    items: GalleryItem[];
};

export function PublicGalleryGrid({ items }: PublicGalleryGridProps) {
    return (
        <div className="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
            {items.map((item, i) => (
                <Reveal key={item.id} delay={(i % 6) * 0.05}>
                    <figure
                        className={`group relative overflow-hidden rounded-2xl border border-slate-200/60 dark:border-white/10 ${item.span ?? ''}`}
                    >
                        <div className="relative min-h-[10rem] sm:min-h-[12rem]">
                            <img
                                src={item.image}
                                alt={item.title}
                                className="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110"
                                loading="lazy"
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-[#071526]/85 via-transparent to-transparent opacity-90 transition group-hover:opacity-100" />
                            <figcaption className="absolute inset-x-0 bottom-0 p-4">
                                <span className="text-[10px] font-bold uppercase tracking-wider text-amber-400">
                                    {item.category}
                                </span>
                                <p className="mt-0.5 text-sm font-semibold text-white">{item.title}</p>
                            </figcaption>
                        </div>
                    </figure>
                </Reveal>
            ))}
        </div>
    );
}
