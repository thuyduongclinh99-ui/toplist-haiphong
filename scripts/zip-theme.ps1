# Packs a theme or plugin folder into dist/<folder>.zip with forward-slash
# entry names (Compress-Archive on Windows PowerShell 5.1 writes backslashes,
# which WordPress on Linux hosts cannot unpack correctly).
param(
	[string]$Source = 'theme/toplist-haiphong',
	# Relative folder (forward slashes) left out of the zip, e.g. 'data/images'.
	[string]$Exclude = ''
)
$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression, System.IO.Compression.FileSystem

$root   = Split-Path -Parent $PSScriptRoot
$source = (Resolve-Path (Join-Path $root $Source)).Path
$name   = Split-Path -Leaf $source
$dist   = Join-Path $root 'dist'
$target = Join-Path $dist "$name.zip"

New-Item -ItemType Directory -Force $dist | Out-Null
if (Test-Path $target) { Remove-Item $target -Confirm:$false }

$zip = [System.IO.Compression.ZipFile]::Open($target, 'Create')
try {
	Get-ChildItem $source -Recurse -File | ForEach-Object {
		$relative = $_.FullName.Substring($source.Length + 1).Replace('\', '/')
		if ($Exclude -and $relative.StartsWith("$Exclude/")) { return }
		[System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, "$name/$relative") | Out-Null
	}
} finally {
	$zip.Dispose()
}
Write-Output "Created $target"
