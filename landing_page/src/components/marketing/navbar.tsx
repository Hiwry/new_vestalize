"use client";

import { cn } from "@/utils";
import { MenuIcon, XIcon } from "lucide-react";
import Link from "next/link";
import { useEffect, useState } from 'react';
import Icons from "../global/icons";
import ThemeToggle from "../global/theme-toggle";
import { Button } from "../ui/button";
import MobileMenu from "./mobile-menu";
import { NAV_LINKS, Routes } from "@/constants";

const Navbar = () => {

    const [isOpen, setIsOpen] = useState<boolean>(false);

    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }

        return () => {
            document.body.style.overflow = '';
        };
    }, [isOpen]);

    return (
        <div className="relative w-full h-full">
            <div className="z-100 hidden lg:block fixed pointer-events-none inset-x-0 h-[88px] bg-white/85 backdrop-blur-sm shadow-[0_12px_30px_rgba(15,18,34,0.08)] [mask:linear-gradient(to_bottom,#000_25%,transparent_calc(100%-25%))]"></div>
            <header
                className={cn(
                    "fixed top-4 inset-x-0 mx-auto max-w-6xl px-2 md:px-12 z-100 transition-all duration-300 ease-in-out",
                    isOpen ? "h-[calc(100dvh-2rem)]" : "h-14 md:h-16"
                )}
            >
                <div className="backdrop-blur-xl rounded-xl lg:rounded-full border border-border bg-surface supports-[backdrop-filter]:bg-surface/95 h-full flex flex-col overflow-hidden relative shadow-card">
                    <div className="flex items-center justify-between w-full px-4 min-h-14 md:min-h-16 shrink-0 pb-1">
                        <div className="flex items-center flex-1 lg:flex-none">
                            <Link href={Routes.Home} className="text-lg font-semibold text-text-primary group">
                                <Icons.wordmark className="w-auto h-5 transition-all group-hover:drop-shadow-[0_0_8px_rgba(0,85,255,0.6)]" />
                            </Link>
                        </div>

                        <div className="lg:flex items-center hidden gap-1 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                            {NAV_LINKS.map((item, index) => (
                                <Link
                                    key={index}
                                    href={item.href}
                                    className="text-sm text-text-secondary hover:text-text-primary font-medium transition-colors px-3 py-1.5"
                                >
                                    {item.label}
                                </Link>
                            ))}
                        </div>

                        <div className="flex items-center gap-2 lg:gap-3">
                            <ThemeToggle />
                            <Link href={Routes.Dashboard}>
                                <Button variant="white" className="hidden lg:flex">
                                    Start for free
                                </Button>
                            </Link>
                            <Button
                                size="icon-sm"
                                variant="ghost"
                                onClick={() => setIsOpen((prev) => !prev)}
                                className="lg:hidden"
                            >
                                {isOpen ? <XIcon className="size-4 duration-300" /> : <MenuIcon className="size-4 duration-300" />}
                            </Button>
                        </div>
                    </div>

                    <MobileMenu isOpen={isOpen} setIsOpen={setIsOpen} />
                </div>
            </header>
        </div>
    )
};

export default Navbar;
