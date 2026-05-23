import { PublicSectionHeader } from './PublicSectionHeader';
import { PublicTestimonialCard } from './PublicTestimonialCard';
import { communityTestimonials } from '@/Components/Public/data/publicSiteContent';
import type { CmsTestimonial } from '@/types/cms';

export function CommunityTestimonialsSection({ items }: { items?: CmsTestimonial[] }) {
    const testimonials =
        items && items.length > 0
            ? items.map((t) => ({
                  quote: t.quote,
                  name: t.name,
                  role: t.role,
                  org: t.org ?? undefined,
                  photo: t.photo ?? undefined,
              }))
            : communityTestimonials;
    return (
        <section className="section-institutional-alt section-separator py-20 sm:py-28">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <PublicSectionHeader
                    eyebrow="Testimonios"
                    title="Lo que dice nuestra comunidad"
                    description="Familias, docentes y egresados comparten la experiencia Horizonte."
                    align="center"
                    className="mb-12"
                />
                <div className="grid gap-6 md:grid-cols-3">
                    {testimonials.map((t, i) => (
                        <PublicTestimonialCard key={t.name} {...t} delay={i * 0.08} />
                    ))}
                </div>
            </div>
        </section>
    );
}
