<?php

return new class {
    public function RenderHTML()
    {
        return '
                <table width=\'100%\' height=\'100%\'>
                    <tr align=center valign=\'top\'><td></td></tr>
                </table>
                ';
    }

    public function Formular()
    {
        $this->title = 'i-Educar';
        $this->processoAp = 68;
    }
};
