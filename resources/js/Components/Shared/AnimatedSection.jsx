import { motion } from 'framer-motion';

const animations = {
    fadeUp: {
        initial: { opacity: 0, y: 30 },
        whileInView: { opacity: 1, y: 0 },
        viewport: { once: true, margin: '-50px' },
        transition: { duration: 0.6, ease: 'easeOut' },
    },
    fadeIn: {
        initial: { opacity: 0 },
        whileInView: { opacity: 1 },
        viewport: { once: true, margin: '-50px' },
        transition: { duration: 0.6, ease: 'easeOut' },
    },
    fadeLeft: {
        initial: { opacity: 0, x: -40 },
        whileInView: { opacity: 1, x: 0 },
        viewport: { once: true, margin: '-50px' },
        transition: { duration: 0.6, ease: 'easeOut' },
    },
    fadeRight: {
        initial: { opacity: 0, x: 40 },
        whileInView: { opacity: 1, x: 0 },
        viewport: { once: true, margin: '-50px' },
        transition: { duration: 0.6, ease: 'easeOut' },
    },
    scaleIn: {
        initial: { opacity: 0, scale: 0.9 },
        whileInView: { opacity: 1, scale: 1 },
        viewport: { once: true, margin: '-50px' },
        transition: { duration: 0.5, ease: 'easeOut' },
    },
    staggerContainer: {
        initial: {},
        whileInView: { transition: { staggerChildren: 0.1 } },
        viewport: { once: true, margin: '-50px' },
    },
    staggerItem: {
        initial: { opacity: 0, y: 20 },
        whileInView: { opacity: 1, y: 0 },
        transition: { duration: 0.4, ease: 'easeOut' },
    },
};

export default function AnimatedSection({ animation = 'fadeUp', children, className = '', ...props }) {
    const anim = animations[animation] || animations.fadeUp;
    return (
        <motion.div
            initial={anim.initial}
            whileInView={anim.whileInView}
            viewport={anim.viewport}
            transition={anim.transition}
            className={className}
            {...props}
        >
            {children}
        </motion.div>
    );
}

export function AnimatedCard({ children, className = '', index = 0 }) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: '-50px' }}
            transition={{ duration: 0.5, delay: index * 0.1, ease: 'easeOut' }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

export { animations };
