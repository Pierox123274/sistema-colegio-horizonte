import { motion, useInView, useReducedMotion, useSpring, useTransform } from 'framer-motion';
import { useEffect, useRef } from 'react';

type AnimatedCounterProps = {
    value: number;
    suffix?: string;
    className?: string;
};

export function AnimatedCounter({ value, suffix = '', className = '' }: AnimatedCounterProps) {
    const ref = useRef<HTMLSpanElement>(null);
    const inView = useInView(ref, { once: true, margin: '-40px' });
    const reduce = useReducedMotion();
    const spring = useSpring(0, { stiffness: 60, damping: 18 });
    const display = useTransform(spring, (v) => `${Math.round(v)}${suffix}`);

    useEffect(() => {
        if (inView || reduce) {
            spring.set(value);
        }
    }, [inView, reduce, spring, value]);

    if (reduce) {
        return (
            <span ref={ref} className={className}>
                {value}
                {suffix}
            </span>
        );
    }

    return (
        <motion.span ref={ref} className={className}>
            {display}
        </motion.span>
    );
}
