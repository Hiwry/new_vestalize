"use client";

import { Toaster } from '@/components/ui/sonner';
import { ThemeProvider } from 'next-themes';
import React from 'react';

const Providers = ({ children }: { children: React.ReactNode }) => {
    return (
        <ThemeProvider
            attribute="class"
            defaultTheme="light"
            enableSystem={false}
            disableTransitionOnChange
        >
            <Toaster />
            {children}
        </ThemeProvider>
    );
};

export default Providers;
