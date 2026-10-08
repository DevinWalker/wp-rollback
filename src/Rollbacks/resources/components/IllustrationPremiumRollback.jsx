/**
 * Decorative illustration of a premium plugin being rolled back from a
 * troubled current version to the previous one.
 *
 * @param {Object} props           Component properties
 * @param {string} props.className Additional class for the SVG element
 * @return {JSX.Element} The illustration
 */
const IllustrationPremiumRollback = ( { className, ...props } ) => {
    return (
        <svg
            className={ className }
            viewBox="0 0 344 158"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            aria-hidden="true"
            focusable="false"
            { ...props }
        >
            { /* Sparkles */ }
            <path d="M186 23q0 7 7 7-7 0-7 7 0-7-7-7 7 0 7-7Z" fill="#ff61ef" />
            <path d="M9 57q0 5 5 5-5 0-5 5 0-5-5-5 5 0 5-5Z" fill="#a972f9" />
            <path d="M190 128q0 4 4 4-4 0-4 4 0-4-4-4 4 0 4-4Z" fill="#707eff" />
            <circle cx="201" cy="47" r="2" fill="#a972f9" />
            <circle cx="6" cy="118" r="2" fill="#ff61ef" />
            <circle cx="338" cy="118" r="2" fill="#c5cdf8" />
            <circle cx="304" cy="36" r="1.5" fill="#c5cdf8" />

            { /* Current version: the update that caused trouble */ }
            <rect
                x="206"
                y="64"
                width="122"
                height="66"
                rx="10"
                fill="#fff"
                stroke="#c3c4c7"
                strokeWidth="1.5"
                strokeDasharray="5 4"
            />
            <rect x="216" y="74" width="28" height="28" rx="7" fill="#dcdcde" />
            <path
                d="M226.5 81v4M233.5 81v4M224 85h12v3a6 6 0 0 1-12 0v-3ZM230 94v3"
                stroke="#fff"
                strokeWidth="1.6"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
            <rect x="252" y="76" width="48" height="6" rx="3" fill="#dcdcde" />
            <rect x="252" y="89" width="34" height="14" rx="7" fill="#f0f0f1" />
            <text x="269" y="99.5" textAnchor="middle" fontSize="9" fontWeight="600" fill="#646970">
                5.8
            </text>
            <rect x="216" y="113" width="84" height="5" rx="2.5" fill="#e6e6e8" />
            <circle cx="328" cy="64" r="10" fill="#f6b53c" stroke="#fff" strokeWidth="2.5" />
            <rect x="326.9" y="58.5" width="2.2" height="7" rx="1.1" fill="#fff" />
            <circle cx="328" cy="68.8" r="1.3" fill="#fff" />

            { /* Earlier versions kept in the vault */ }
            <rect x="40" y="60" width="110" height="60" rx="10" fill="#f3f5fe" stroke="#dfe4fb" />
            <rect x="30" y="66" width="130" height="60" rx="10" fill="#e8ecfd" stroke="#c5cdf8" />

            { /* Previous version: the premium plugin being restored */ }
            <ellipse cx="95" cy="152" rx="64" ry="4" fill="#3858e9" fillOpacity="0.1" />
            <rect x="20" y="73" width="150" height="76" rx="10" fill="#fff" stroke="#3858e9" strokeWidth="1.5" />
            <rect x="32" y="85" width="34" height="34" rx="8" fill="url(#wpr-premium-rollback-tile)" />
            <path
                d="M44.5 93v5M53.5 93v5M41 98h16v4a8 8 0 0 1-16 0v-4ZM49 110v4"
                stroke="#fff"
                strokeWidth="1.8"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
            <rect x="76" y="87" width="58" height="6" rx="3" fill="#c5cdf8" />
            <rect x="76" y="99" width="38" height="5" rx="2.5" fill="#e1e6fc" />
            <rect x="76" y="111" width="38" height="16" rx="8" fill="#e8ecfd" />
            <text x="95" y="122.5" textAnchor="middle" fontSize="10" fontWeight="600" fill="#183ad6">
                5.7
            </text>
            <rect x="32" y="131" width="70" height="5" rx="2.5" fill="#e1e6fc" />
            <circle cx="150" cy="131" r="8" fill="#3858e9" />
            <path
                d="m146.3 131.2 2.6 2.6 4.8-5.2"
                stroke="#fff"
                strokeWidth="1.8"
                strokeLinecap="round"
                strokeLinejoin="round"
            />

            { /* Premium badge */ }
            <circle cx="170" cy="73" r="13" fill="url(#wpr-premium-rollback-badge)" stroke="#fff" strokeWidth="2.5" />
            <path
                d="m170 65.8 1.82 4.69 5.03.29-3.9 3.18 1.28 4.86L170 76.1l-4.23 2.72 1.28-4.86-3.9-3.18 5.03-.29L170 65.8Z"
                fill="#fff"
            />

            { /* Rollback arrow: one circular arc, with the head aligned to its end tangent */ }
            <path
                d="M267 60A96 96 0 0 0 100.4 49.8"
                stroke="url(#wpr-premium-rollback-arrow)"
                strokeWidth="2.5"
                strokeLinecap="round"
            />
            <path
                d="M95.5 57.3 105.4 53.1 95.4 46.5Z"
                fill="#3858e9"
                stroke="#3858e9"
                strokeWidth="1.5"
                strokeLinejoin="round"
            />

            <defs>
                <linearGradient
                    id="wpr-premium-rollback-tile"
                    x1="32"
                    y1="85"
                    x2="66"
                    y2="119"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop stopColor="#707eff" />
                    <stop offset="1" stopColor="#a972f9" />
                </linearGradient>
                <linearGradient
                    id="wpr-premium-rollback-badge"
                    x1="157"
                    y1="60"
                    x2="183"
                    y2="86"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop stopColor="#a972f9" />
                    <stop offset="1" stopColor="#ff61ef" />
                </linearGradient>
                <linearGradient
                    id="wpr-premium-rollback-arrow"
                    x1="267"
                    y1="30"
                    x2="95"
                    y2="30"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop stopColor="#c3c4c7" />
                    <stop offset="0.45" stopColor="#a972f9" />
                    <stop offset="1" stopColor="#3858e9" />
                </linearGradient>
            </defs>
        </svg>
    );
};

export default IllustrationPremiumRollback;
