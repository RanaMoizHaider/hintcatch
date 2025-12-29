import { Search } from 'lucide-react';
import { useCallback, useEffect, useRef } from 'react';

interface SearchInputProps {
    value: string;
    onChange: (value: string) => void;
    onSearch?: (value: string) => void;
    placeholder?: string;
    className?: string;
}

function useDebouncedCallback<T extends (...args: any[]) => any>(
    callback: T,
    delay: number,
) {
    const timeoutRef = useRef<NodeJS.Timeout | null>(null);

    const debouncedCallback = useCallback(
        (...args: Parameters<T>) => {
            if (timeoutRef.current) {
                clearTimeout(timeoutRef.current);
            }

            timeoutRef.current = setTimeout(() => {
                callback(...args);
            }, delay);
        },
        [callback, delay],
    );

    useEffect(() => {
        return () => {
            if (timeoutRef.current) {
                clearTimeout(timeoutRef.current);
            }
        };
    }, []);

    return debouncedCallback;
}

export function SearchInput({
    value,
    onChange,
    onSearch,
    placeholder = 'Search...',
    className = '',
}: SearchInputProps) {
    const handleSearch = useDebouncedCallback((term: string) => {
        if (onSearch) {
            onSearch(term);
        }
    }, 300);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newValue = e.target.value;
        onChange(newValue);
        handleSearch(newValue);
    };

    return (
        <div className={`relative ${className}`}>
            <div className="flex items-center border-2 border-ds-border bg-ds-bg-card focus-within:border-ds-text-muted">
                <Search className="ml-3 h-4 w-4 shrink-0 text-ds-text-muted" />
                <input
                    type="text"
                    value={value}
                    onChange={handleChange}
                    placeholder={placeholder}
                    className="w-full bg-transparent px-2 py-2 text-sm text-ds-text-primary placeholder-ds-text-muted focus:outline-none"
                />
            </div>
        </div>
    );
}
