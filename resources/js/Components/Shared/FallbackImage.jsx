import { useState } from 'react';

export default function FallbackImage({ src, alt, className, fallback = '/Logo/Square-White-BG.png', ...props }) {
    const [imgSrc, setImgSrc] = useState(src);
    const [hasFailed, setHasFailed] = useState(false);

    const handleError = () => {
        if (!hasFailed) {
            setHasFailed(true);
            setImgSrc(fallback);
        }
    };

    return <img src={imgSrc} alt={alt || ''} className={className} onError={handleError} {...props} />;
}
