import { PublicEditorialCard } from './PublicEditorialCard';
import { PublicSectionHeader } from './PublicSectionHeader';
import { schoolLifeEditorial } from '@/Components/Public/data/publicSiteContent';
import { publicImage } from '@/Components/Public/data/publicImages';

export function SchoolLifeEditorialSection() {
    return (
        <section className="section-institutional py-20 sm:py-28">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <PublicSectionHeader
                    eyebrow="Vida escolar"
                    title="Vida escolar que inspira"
                    description="Experiencias que complementan el aula: deporte, arte, ciencia, liderazgo y momentos que permanecen."
                    align="center"
                    className="mb-14"
                />
                <div className="grid gap-4 md:grid-cols-3">
                    {schoolLifeEditorial.map((item, i) => (
                        <PublicEditorialCard
                            key={item.title}
                            image={publicImage(item.imageKey)}
                            title={item.title}
                            description={item.description}
                            href={route(item.href)}
                            size={item.size ?? 'default'}
                            delay={i * 0.06}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
}
