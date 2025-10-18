import React from 'react';
import { Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import FavIcons from '@/Layouts/Partials/FavIcons';

const GuestLayout = ({ children }) => {
    return (
        <>
            <FavIcons />
            <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
                <div className="uppercase text-blue-400">
                    <Link href="/">
                        <ApplicationLogo />
                    </Link>
                </div>
                <div className="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                    {children}
                </div>
            </div>
        </>
    );
};

export default GuestLayout;

