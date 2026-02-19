"use client";

import { cn } from "@/utils";
import { MoonStarIcon, SunMediumIcon } from "lucide-react";
import { useTheme } from "next-themes";
import { useEffect, useState } from "react";
import { Button } from "../ui/button";

interface Props {
  className?: string;
}

const ThemeToggle = ({ className }: Props) => {
  const { theme, setTheme, resolvedTheme } = useTheme();
  const [mounted, setMounted] = useState(false);

  useEffect(() => setMounted(true), []);

  const currentTheme = theme === "system" ? resolvedTheme : theme;
  const isDark = currentTheme === "dark";

  if (!mounted) {
    return (
      <Button
        size="icon-sm"
        variant="outline"
        className={cn(
          "border-transparent bg-transparent shadow-none opacity-50 pointer-events-none",
          className,
        )}
        aria-hidden
      >
        <SunMediumIcon className="size-4" />
      </Button>
    );
  }

  return (
    <Button
      type="button"
      size="icon-sm"
      variant="outline"
      aria-label={isDark ? "Ativar tema claro" : "Ativar tema escuro"}
      onClick={() => setTheme(isDark ? "light" : "dark")}
      className={cn(
        "border-border bg-surface text-text-primary shadow-card",
        "hover:bg-brand-soft hover:border-border backdrop-blur supports-[backdrop-filter]:bg-surface/80",
        className,
      )}
    >
      {isDark ? <SunMediumIcon className="size-4" /> : <MoonStarIcon className="size-4" />}
    </Button>
  );
};

export default ThemeToggle;
