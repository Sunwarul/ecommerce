import React from 'react'; 
import { Button } from 'primereact/button';

export default function Welcome() {
    return (
        <div className="flex flex-column md:flex-row justify-content-between my-5 bg-red-400">
            <Button type="button" label="Button" className="mb-3 md:mb-0"></Button>
        </div>
    )
}