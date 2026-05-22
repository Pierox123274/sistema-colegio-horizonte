import { motion } from 'framer-motion';
import type { PropsWithChildren } from 'react';

/** Transición suave al cargar cada página pública. */
export function PublicPageFade({ children }: PropsWithChildren) {
    return (
        <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.35, ease: 'easeOut' }}
        >
            {children}
        </motion.div>
    );
}
